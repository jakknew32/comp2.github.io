# AI Coding Guidelines for comp2.github.io

## Project Overview
This is a static GitHub Pages website for Chiangmai Freshmilk's photo printing and shipping label service. The entire application is contained in a single `index.html` file with embedded CSS and JavaScript.

## Architecture
- **Single-page application**: All functionality in one HTML file
- **No build process**: Direct static hosting on GitHub Pages
- **Vanilla JavaScript**: No frameworks or external JS libraries
- **Inline CSS**: All styles embedded in `<style>` tag

## Key Components
- **Photo Layout**: A4 pages with two rows
  - Row 1: Single full-width photo (drag/drop/upload/paste)
  - Row 2: Grid of photos (configurable count, default 3)
- **Shipping Labels**: Modal-based Thai language labels with fixed sender info
- **Print Functionality**: Uses `window.print()` with custom CSS media queries

## Code Patterns
- **Image Handling**: Convert files to base64 data URLs for immediate display
- **Page Generation**: Dynamic creation of `.a4-page` divs with specific mm dimensions
- **Thai Localization**: Hardcoded Thai text and 'Sarabun' font family
- **Modal System**: Inline creation of modal overlays with event listeners

## Specific Conventions
- **Dimensions**: Use mm units for print layouts (210mm × 297mm A4, 101.6mm × 152.4mm labels)
- **Company Info**: Always include "บริษัท เชียงใหม่เฟรชมิลค์ จำกัด" as sender
- **Timestamp Format**: Thai Buddhist calendar (year + 543) with abbreviated months
- **Photo Containers**: Use `object-fit: cover` for row 2, `contain` for row 1
- **Print Styles**: Hide controls, remove shadows, set page breaks

## Development Workflow
- **Testing**: Open `index.html` directly in browser
- **Debugging**: Use browser dev tools; no console logging in production
- **Deployment**: Push to `main` branch for automatic GitHub Pages deployment

## File Structure
```
index.html          # Main application
README.md           # Basic project description
souce.xlsx          # Source data (likely for labels)
.github/            # GitHub-specific files
  copilot-instructions.md
```

## Common Tasks
- **Add features**: Modify JavaScript functions in `<script>` tag
- **Style changes**: Update CSS in `<style>` tag
- **Content updates**: Edit hardcoded Thai strings and company information
- **Layout tweaks**: Adjust `.photo-row` and `.photo-container` classes</content>
<parameter name="filePath">c:\Users\jakkaphan.s\comp2.github.io\.github\copilot-instructions.md