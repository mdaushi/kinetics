---
title: Customization
description: Guide for customizing the table styling.
---

Kinetics is built on top of **TanStack Table** (as a headless UI core) and renders built-in components with a design based on **shadcn/ui** using **Tailwind CSS** utilities.

## Tailwind CSS Integration

Ensure that your `tailwind.config.js` or `app.css` file (for Tailwind v4) has been configured to scan utility classes within the `@mdaushi/kinetics-react` package. Without this step, the shadcn/ui styles will not be read and your table will appear "bare" without styling.

*(See the [Installation](/getting-started/installation) section for detailed Tailwind configuration guides).*

## Global Customization

By default, the table takes its default colors from standard shadcn/ui CSS variables. If your project has already implemented a theming system using variables like `--primary`, `--border`, or `--background`, the Kinetics `<Table />` component will seamlessly adapt to your application's color palette.

## Advanced Cell Customization (Upcoming)

Currently, Kinetics' main focus is zero-boilerplate, where the `<Table />` component handles almost all use cases out-of-the-box.

Kinetics provides full access to the underlying table instance of TanStack Table via a Hook mechanism (`useTable`) or access properties, which will be further documented in upcoming releases. This will allow developers to override cell rendering customly, if features like standard `TextColumn` and `Badge` are insufficient for your application's specific UI complexity needs.
