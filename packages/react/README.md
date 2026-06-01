# @mdaushi/kinetics-react

React adapter for [Kinetics](https://github.com/mdaushi/kinetics-datatable)

## Installation

```bash
npm install @mdaushi/kinetics-react
```

## Usage

```tsx
import { Table, TableProps } from '@mdaushi/kinetics-react';

export default function UsersIndex({ table }: { table: TableProps<User> }) {
  return <Table table={table} searchPlaceholder="Search users..." />;
}
```

## Documentation

Full documentation is available [here](https://github.com/mdaushi/kinetics/blob/main/docs/index.md).

## License

MIT
