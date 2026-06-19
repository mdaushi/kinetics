<?php

namespace Kinetics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Kinetics\Columns\Column;
use Kinetics\Contracts\FilterInterface;
use Kinetics\Contracts\PipeInterface;
use Kinetics\Exceptions\InvalidPipeException;
use Kinetics\Pipes\FilterPipe;
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

    /** @var FilterInterface[] */
    private array $filters = [];

    private array $actions = [];

    private array $bulkActions = [];

    /** @var class-string<PipeInterface>[]|PipeInterface[] */
    private array $extraPipes = [];

    /** @var class-string<PipeInterface>[] */
    private array $removedPipes = [];

    private TableConfig $config;

    private int $debounce = 500;

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
        $this->config = TableConfig::fromConfig();
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
     * @param  Column[]  $columns
     */
    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Define global actions.
     */
    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Define global bulk actions (active when rows are selected).
     */
    public function bulkActions(array $actions): static
    {
        $this->bulkActions = $actions;

        return $this;
    }

    /**
     * Define filter definitions.
     *
     * @param  FilterInterface[]  $filters
     */
    public function filters(array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Set per-page default dan max.
     */
    public function perPage(int $default, int $max = 100): static
    {
        $this->config = $this->config->with(defaultPerPage: $default, maxPerPage: $max);

        return $this;
    }

    /**
     * Set debounce delay (ms) for search and filter inputs.
     * Default: 500ms
     */
    public function debounce(int $ms): static
    {
        $this->debounce = $ms;

        return $this;
    }

    /**
     * Default sort ketika tidak ada request sort.
     */
    public function defaultSort(string $column, string $direction = 'desc'): static
    {
        $this->config = $this->config->with(defaultSort: $column, defaultDirection: $direction);

        return $this;
    }

    /**
     * Add a custom pipe. It can be a class-string or an instance.
     * Custom pipes are inserted BEFORE PaginatePipe.
     *
     * @param  array<class-string<PipeInterface>|PipeInterface>  $pipes
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
     * @param  array<class-string<PipeInterface>>  $pipes
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

        if (
            ! (is_string($pipe)
                ? is_subclass_of($pipe, PipeInterface::class)
                : $pipe instanceof PipeInterface)
        ) {
            throw InvalidPipeException::doesNotImplementInterface($class);
        }
    }

    // Output methods

    /**
     * Run the pipeline and return a plain array.
     * Use this for Inertia responses or any place that expects a plain array.
     *
     *   // Inertia
     *   return Inertia::render('Users/Index', ['table' => Table::model(User::class)->make()]);
     *
     *   // JSON API
     *   return response()->json(Table::model(User::class)->make());
     */
    public function make(): array
    {
        return $this->build()->toArray();
    }

    /**
     * Run the pipeline and return a TableResult instance.
     * Prefer this over make() when you need to inspect specific parts
     * of the result — for example in tests or JSON APIs:
     *
     *   $result = Table::model(User::class)->get();
     *
     *   $result->getData();        // transformed rows
     *
     *   $result->getTotal();       // total row count
     *
     *   $result->getMeta();        // pagination meta
     *
     *   $result->getPaginator();   // raw LengthAwarePaginator
     *
     *   // Works directly with response()->json() via JsonSerializable
     *   return response()->json($result);
     */
    public function get(): TableResult
    {
        return $this->build();
    }

    /**
     * Run the pipeline and return only the raw LengthAwarePaginator,
     * bypassing TableResult formatting entirely.
     * Useful when you need full control over the response shape.
     */
    public function paginate(): LengthAwarePaginator
    {
        $this->buildContext();

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
     * so that it can be retrieved by pipes via TableContext::getForQuery($query).
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
            filters: $this->filters,
            actions: $this->actions,
            bulkActions: $this->bulkActions,
        );

        $context->setMeta('debounce', $this->debounce);

        TableContext::setForQuery($this->query, $context);

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
        $before = array_filter(
            $this->defaultPipes,
            fn ($p) => $p !== PaginatePipe::class && ! in_array($p, $this->removedPipes)
        );

        $hasPaginate = ! in_array(PaginatePipe::class, $this->removedPipes);

        $pipes = array_values($before);

        // Custom pipes — setelah sort/search/filter, sebelum paginate
        foreach ($this->extraPipes as $pipe) {
            $pipeClass = is_string($pipe) ? $pipe : get_class($pipe);

            if (! in_array($pipeClass, $this->removedPipes)) {
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
