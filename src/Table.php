<?php

namespace Kinetics;

use Kinetics\Pipes\FilterPipe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Kinetics\Columns\Column;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Exceptions\InvalidPipeException;
use Kinetics\Pipes\PaginatePipe;
use Kinetics\Pipes\SearchPipe;
use Kinetics\Pipes\SortPipe;
use Kinetics\Resources\TableResult;
use Kinetics\Support\TableConfig;
use Kinetics\Support\TableContext;

class Table
{
    protected Builder $query;
    protected Request $request;

    /** @var Column[] */
    private array $columns = [];

    /** @var class-string<PipeInterface>[]|PipeInterface[] */
    private array $extraPipes   = [];

    /** @var class-string<PipeInterface>[] */
    private array $removedPipes = [];

    private TableConfig $config;

    // Default pipe is always present — this order is IMPORTANT
    private array $defaultPipes = [
        SortPipe::class,
        SearchPipe::class,
        FilterPipe::class,
        PaginatePipe::class,
    ];

    // Constructor (private — gunakan ::model() atau ::query())
    private function __construct(Builder $query, Request $request)
    {
        $this->query = $query;
        $this->request = $request;
        $this->config = new TableConfig();
    }

    // Static entry points
    public static function model(string $modelClass): static
    {
        $model = new $modelClass;

        if (! $model instanceof Model) {
            throw new \InvalidArgumentException("{$modelClass} must be an Eloquent Model.");
        }

        return new static(
            $model->newQuery(),
            request(),
        );
    }

    public static function query(Builder $query): static
    {
        return new static($query, request());
    }

    /**
     * Define column definitions.
     *
     * @param Column[] $columns
     */
    public function columns(array $columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Set per-page default dan max.
     */
    public function perPage(int $default, int $max = 100): static
    {
        $this->config = new TableConfig(
            defaultPerPage: $default,
            maxPerPage: $max,
            defaultSort: $this->config->defaultSort,
            defaultDirection: $this->config->defaultDirection,
        );
        return $this;
    }

    /**
     * Default sort ketika tidak ada request sort.
     */
    public function defaultSort(string $column, string $direction = 'desc'): static
    {
        $this->config = new TableConfig(
            defaultPerPage: $this->config->defaultPerPage,
            maxPerPage: $this->config->maxPerPage,
            defaultSort: $column,
            defaultDirection: $direction,
        );
        return $this;
    }

    /**
     * Add a custom pipe. It can be a class-string or an instance.
     * Custom pipes are inserted BEFORE PaginatePipe.
     *
     * @param array<class-string<PipeInterface>|PipeInterface> $pipes
     */
    public function pipes(array $pipes): static
    {
        foreach ($pipes as $pipe) {
            $this->validatePipe($pipe);
        }

        $this->extraPipes = array_merge($this->extraPipes, $pipes);
        return $this;
    }

    /**
     * Remove unnecessary default pipes.
     *
     * @param array<class-string<PipeInterface>> $pipes
     */
    public function withoutPipes(array $pipes): static
    {
        $this->removedPipes = array_merge($this->removedPipes, $pipes);
        return $this;
    }

    /**
     * Apply additional Eloquent scope/constraints to the base query.
     *
     * Contoh:
     *   ->tap(fn($q) => $q->where('tenant_id', auth()->user()->tenant_id))
     */
    public function tap(\Closure $callback): static
    {
        $callback($this->query);
        return $this;
    }

    /**
     * Override request (useful for testing).
     */
    public function withRequest(Request $request): static
    {
        $this->request = $request;
        return $this;
    }

    private function validatePipe(mixed $pipe): void
    {
        $class = is_string($pipe) ? $pipe : get_class($pipe);

        if (! (is_string($pipe)
            ? is_subclass_of($pipe, PipeInterface::class)
            : $pipe instanceof PipeInterface)) {
            throw InvalidPipeException::doesNotImplementInterface($class);
        }
    }

    // Output methods

    /**
     * Run the pipeline and return a ready-to-use array in Inertia.
     *
     * Contoh:
     *   return Inertia::render('Users/Index', [
     *       'table' => Table::model(User::class)->columns([...])->make(),
     *   ]);
     */
    public function make(): array
    {
        return $this->build()->toArray();
    }

    /**
     * Return raw TableResult (for testing or API JSON).
     */
    public function get(): TableResult
    {
        return $this->build();
    }

    /**
     * Return only the paginator (bypass TableResult formatting).
     */
    public function paginate(): LengthAwarePaginator
    {
        $context = $this->buildContext();
        return $this->runPipeline();
    }

    // Internal: build context, assemble pipeline, run

    private function build(): TableResult
    {
        $context = $this->buildContext();
        $paginator = $this->runPipeline();

        return new TableResult($paginator, $context, $this->columns);
    }

    /**
     * Create a TableContext and "pass" it to the model
     * so that it can be retrieved by pipes via $query->getModel()->datatableContext.
     *
     * This is the cleanest way to pass context to pipes
     * without having to change the signature of Pipeline::send().
     */
    private function buildContext(): TableContext
    {
        $context = new TableContext(
            query: $this->query,
            request: $this->request,
            config: $this->config,
            columns: $this->columns,
        );

        // Titipkan context ke model — diambil oleh pipes
        $this->query->getModel()->datatableContext = $context;

        return $context;
    }

    /**
     * Assemble the final pipe list and run the pipeline.
     */
    private function runPipeline(): LengthAwarePaginator
    {
        $pipes = $this->assemblePipes();

        return app(Pipeline::class)
            ->send($this->query)
            ->through($pipes)
            ->thenReturn();
    }

    /**
     * Raft pipes: default + extra (inserted before PaginatePipe) - removed.
     */
    private function assemblePipes(): array
    {
        // Pisahkan PaginatePipe dari default pipes agar custom pipes bisa disisipkan sebelum paginator berjalan
        $before  = array_filter(
            $this->defaultPipes,
            fn($p) => $p !== PaginatePipe::class && ! in_array($p, $this->removedPipes)
        );

        $hasPaginate = ! in_array(PaginatePipe::class, $this->removedPipes);

        $pipes = array_values($before);

        // Custom pipes — setelah sort/search/filter, sebelum paginate
        foreach ($this->extraPipes as $pipe) {
            if (! in_array(is_string($pipe) ? $pipe : get_class($pipe), $this->removedPipes)) {
                $pipes[] = $pipe;
            }
        }

        if ($hasPaginate) {
            $pipes[] = PaginatePipe::class;
        }

        // Resolve class-string pipes via container (untuk dependency injection)
        return array_map(function ($pipe) {
            if (is_string($pipe)) {
                return app($pipe);
            }
            return $pipe;
        }, $pipes);
    }
}
