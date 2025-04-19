# User Preferences

## General Guidelines
- Always consider these preferences in all future conversations
- Follow these requirements without needing explicit reminders
- Maintain these settings unless specifically instructed to change them
- Respond to the user in Chinese language
- Ensure all code examples and implementations do not contain Chinese characters
- Do not make any modifications to files or content without explicit instructions
- The game template (game-template.html) should never be modified without specific instructions
- All game pages must be generated strictly following the structure and layout of game-template-1.0.html stored in src/backups/v1.0/
- All changes to templates should be tracked with proper versioning in the src/backups/ directory

## Content Requirements
- Always include the exact marketing tagline "Play various games for free on sonice.online. Thanks for your support and sharing!" on all game pages without any modifications
- This marketing tagline must be displayed in the dedicated marketing area (the area indicated with {{game.description}} in the template), NOT within the actual game description content
- The marketing tagline should appear exactly as specified and must not be changed
- Maintain consistent styling across all game-related templates
- Keep existing SEO metadata structure in place
- Preserve the rating and commenting system functionality
- Ensure all generated pages have proper schema.org markup
- Ensure game controls and game overview sections have appropriate content richness - not too verbose but not too minimal
- The game controls section should include helpful tips for better gameplay experience
- The game overview section should highlight key features of the game in an organized format
- The comment section must be included below the rating section on all game pages

## Technical Implementation
- Follow existing code conventions and styles when making edits
- Use the existing template variables format (e.g., `{{game.title}}`, `{{game.description}}`)
- Maintain compatibility with the current templating system
- Preserve existing JavaScript functionality
- Keep responsive design and accessibility features intact
- Write clean code without any Chinese characters or comments
- Never modify any part of a template or script unless specifically instructed to do so
- The structure, layout, and design of templates in src/backups/v1.0/ must be strictly maintained
- Any improvements or modifications to the template must be explicitly requested and approved
- Reference template versions include game-template-1.0.html and index_1.0.html in src/backups/v1.0/

## File Management
- All template backups are stored in the src/backups/v1.0/ directory
- Current version backups include:
  - game-template-1.0.html: The stable version of the game page template
  - index_1.0.html: The stable version of the main index page
- When creating new versions, use semantic versioning (e.g., v1.1, v2.0) and store in appropriate folders
- Always maintain backwards compatibility unless a major version change is explicitly approved

## Communication Style
- Provide direct, concise responses to queries in Chinese
- Focus on specific implementation details when discussing code
- Include relevant code examples when explaining technical concepts
- Avoid lengthy explanations for basic changes
- Highlight important considerations when suggesting modifications
- Always ask for confirmation before making significant changes to existing content

These preferences will be referenced in all our future conversations to maintain consistency and efficiency. 