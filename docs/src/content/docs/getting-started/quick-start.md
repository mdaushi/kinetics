---
title: Quick Start
description: Create your first datatable with Kinetics.
---

This guide will show you how to create a simple table to display a list of Posts complete with built-in search and sorting features.

## 1. Prepare the Model

Make sure you have an Eloquent model (e.g., `Post`). Kinetics will use this model to automatically query the database.

## 2. Define the Table in the Controller (Laravel)

Inside your controller, use the `Table` class from Kinetics to define which columns you want to display.

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Kinetics\Table;
use Kinetics\Columns\TextColumn;

class PostController extends Controller
{
    public function index()
    {
        $postsTable = Table::model(Post::class)
            ->columns([
                TextColumn::make('title')
                    ->label('Article Title')
                    ->sortable()
                    ->searchable(),
                
                // Supports relationships (dot notation)
                TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->make();

        // Send data to the React component via Inertia
        return Inertia::render('Posts/Index', [
            'postsTable' => $postsTable
        ]);
    }
}
```

> **Note:** The `->make()` function at the end of the chain will execute the pipeline (sorting, searching, pagination) and return a structured data array ready to be consumed by the frontend.

## 3. Render the Table in the Frontend (React)

On the frontend, you only need to call the `<Table />` component and pass the data obtained from the server (Inertia props).

```tsx
import { Head } from '@inertiajs/react';
import { Table } from "@mdaushi/kinetics-react";

export default function PostIndex() {
  return (
    <div className="container mx-auto mt-10">
      <Head title="Posts List" />
      
      <div className="bg-white rounded-lg shadow p-6">
        <h1 className="text-2xl font-bold mb-6">Posts List</h1>
        
        {/* Render the table component by passing the Inertia prop key */}
        <Table table="postsTable" />
      </div>
    </div>
  );
}
```

That's it! With the code above, you already have a fully functional table:
- **Global search** for the `title` and `user.name` columns
- **Sorting** when clicking the column header
- **Pagination** managed entirely by the server

### What's Next
Learn more about the available column types on the [Columns](/columns/overview) page.
