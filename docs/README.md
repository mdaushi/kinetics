# Kinetics Datatable Documentation

This directory contains the official documentation for **Kinetics Datatable**, built with [Astro](https://astro.build/) and [Starlight](https://starlight.astro.build/).

## 🚀 Running Locally

To run the documentation site locally and preview your changes, follow these steps:

1. **Install dependencies**:
   ```bash
   pnpm install
   ```

2. **Start the development server**:
   ```bash
   pnpm dev
   ```

3. **Open in browser**:
   Visit `http://localhost:4321` to view the documentation. The site will automatically reload as you make changes to the markdown files.

## 📁 Project Structure

- `src/content/docs/`: Contains all the English (default) markdown (`.md` or `.mdx`) files.
- `src/content/docs/id/`: Contains the Indonesian translation of the documentation.
- `src/assets/`: Contains static assets like images used in the markdown files.
- `astro.config.mjs`: The configuration file where the sidebar navigation and internationalization (i18n) settings are defined.

## ✍️ Contributing to Docs

When writing or updating documentation:
- Keep the language clear and concise.
- Provide code examples where appropriate.
- Ensure that you update both the English (`src/content/docs/`) and Indonesian (`src/content/docs/id/`) versions of the file.
- Update the sidebar in `astro.config.mjs` if you are adding new pages.

## 🧞 Build Commands

| Command                   | Action                                           |
| :------------------------ | :----------------------------------------------- |
| `pnpm install`            | Installs dependencies                            |
| `pnpm dev`                | Starts local dev server at `localhost:4321`      |
| `pnpm build`              | Builds the production site to `./dist/`          |
| `pnpm preview`            | Previews your build locally, before deploying    |
