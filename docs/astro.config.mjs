// @ts-check
import { defineConfig } from "astro/config";
import starlight from "@astrojs/starlight";

// https://astro.build/config
export default defineConfig({
  integrations: [
    starlight({
      title: "Kinetics",
      social: [
        {
          icon: "github",
          label: "GitHub",
          href: "https://github.com/mdaushi/kinetics",
        },
      ],
      locales: {
        root: {
          label: "English",
          lang: "en",
        },
        id: {
          label: "Bahasa Indonesia",
          lang: "id",
        },
      },
      sidebar: [
        {
          label: "Getting Started",
          items: [
            { label: "Installation", slug: "getting-started/installation" },
            { label: "Quick Start", slug: "getting-started/quick-start" },
          ],
        },
        {
          label: "Table",
          items: [{ label: "Overview", slug: "table/overview" }],
        },
        {
          label: "Columns",
          items: [
            { label: "Overview", slug: "columns/overview" },
            { label: "Text Column", slug: "columns/text" },
            { label: "Action Column", slug: "columns/action" },
          ],
        },
        {
          label: "Filters",
          items: [
            { label: "Overview", slug: "filters/overview" },
            { label: "Text Filter", slug: "filters/text" },
            { label: "Select Filter", slug: "filters/select" },
            { label: "Date Filter", slug: "filters/date" },
            { label: "Number Filter", slug: "filters/number" },
            { label: "Custom Operators", slug: "filters/custom-operators" },
          ],
        },
        {
          label: "Actions",
          items: [
            { label: "Overview", slug: "actions/overview" },
            { label: "Buttons", slug: "actions/buttons" },
            { label: "Groups", slug: "actions/groups" },
          ],
        },
        {
          label: "Data & Pipeline",
          items: [
            { label: "Overview", slug: "pipeline/overview" },
            { label: "Searching", slug: "pipeline/searching" },
            { label: "Sorting", slug: "pipeline/sorting" },
            { label: "Filtering", slug: "pipeline/filtering" },
            { label: "Pagination", slug: "pipeline/pagination" },
            { label: "Custom Pipes", slug: "pipeline/custom-pipes" },
          ],
        },
        {
          label: "Frontend API",
          items: [
            { label: "<Table /> Component", slug: "frontend/table-component" },
            { label: "Customization", slug: "frontend/customization" },
          ],
        },
      ],
    }),
  ],
});
