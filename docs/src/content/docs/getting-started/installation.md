---
title: Installation
description: Kinetics installation guide for Laravel and React.
---

Kinetics consists of two main parts: the **Laravel Package** (Backend) and the **React Package** (Frontend). Both must be installed and configured to work together.

## 1. Backend (Laravel)

Install the Kinetics package for Laravel using Composer:

```bash
composer require mdaushi/kinetics
```

This package provides the `Table` class interface to configure columns, actions, and pipes on the server side.

## 2. Frontend (React)

Kinetics currently supports React (via Inertia.js). Install the frontend package using NPM or your preferred package manager:

```bash
npm install @mdaushi/kinetics-react
```
or
```bash
pnpm add @mdaushi/kinetics-react
```

## 3. Tailwind CSS Configuration

Kinetics is built using [shadcn/ui](https://ui.shadcn.com/) components and uses Tailwind CSS for styling. You need to tell Tailwind to scan the utility classes inside the Kinetics package so they are not purged during the build process.

### Tailwind v4

If you are using Tailwind v4, add the `@source` directive to your main CSS file (e.g., `resources/css/app.css`):

```css
@import "tailwindcss";

@source "../../node_modules/@mdaushi/kinetics-react/dist";
```

### Tailwind v3

If you are still using Tailwind v3, add the package dist path to the `content` array in your `tailwind.config.js` file:

```js
export default {
  content: [
    // ... your existing app paths
    "./node_modules/@mdaushi/kinetics-react/dist/**/*.js",
  ],
};
```

## Publishing Configuration (Optional)

If you want to modify global defaults (such as default pagination size, available pagination options, or default themes), you can publish the Kinetics configuration file to your Laravel config directory:

```bash
php artisan vendor:publish --tag=kinetics-config
```

This will create a `config/kinetics.php` file in your application.

## Next Steps
You're now ready to build your first datatable! Head over to the [Quick Start](/getting-started/quick-start) guide.
