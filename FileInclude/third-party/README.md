# Third-party libraries

Served by the application itself instead of a CDN, so that a visit sends
nothing to other hosts (see the "Stack" section of the main README). The files
are copied unchanged from the npm packages; the version is in the folder name,
so a new version gets a new URL and no browser keeps the old one in cache.

| Folder             | Package                | Files taken                                                                                 | License |
| ------------------ | ---------------------- | ------------------------------------------------------------------------------------------- | ------- |
| `jquery-3.7.1/`    | `jquery@3.7.1`         | `dist/jquery.min.js`, `LICENSE.txt`                                                         | MIT     |
| `jquery-ui-1.14.2/` | `jquery-ui@1.14.2`    | `dist/jquery-ui.min.js`, `dist/themes/base/jquery-ui.min.css` and its `images/`, `LICENSE.txt` | MIT     |

jQuery is used by every page (`searchTable` in `FileInclude/Javascript/JavaScript.js`),
jQuery UI only by the date picker of `FileToBeExecuted/ModuloDomanda.php`.

## Updating a library

1. `npm pack <package>@<version>` in a temporary folder and extract it.
2. Copy the files listed above into a new folder named after the version, and
   delete the old one.
3. Change the paths in the pages (`grep -rn third-party FileToBeExecuted index.php`).
4. `just check`; the unit test `ThirdPartyTest` fails if a referenced file is
   missing or a library folder has no license.
