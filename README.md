# Forum Upload
This plugin extends Winter.Forum to adds front-end user file upload capabilities to it.

It extends the `Topic` component to add a `onFileUpload` handler which:
- store the uploaded files
- attach them to the `Post` model

[File upload Preview](file-upload-preview.webm)

### Under development
This plugin is still under active development and should not be used in a production environment.

### Installation
This plugin can be installed using composer:

```terminal
composer require sunlab/wn-levelup-plugin
```

### How to use it
The plugin doesn't provide any front end component,
you need to customize yourself the way the file should be attached on the frontend side, you can use it with Dropzone.js, a richeditor or any custom implementation.

[You can find here](https://gist.github.com/RomainMazB/b93fa2d1df7f93730c2f67ef13a6bed6) a custom implementation reproducing a GitHub-like mechanism (as shown above).

Basically: you need to trigger an AJAX call for the handler `onFileUpload` sending files (the input name doesn't matter).
The handler will return server-side uploaded files' information containing:
```
    id => the File model id
    originalName => the uploaded original file name
    name => same as originalName without extension
    url => the server-side file's url
```

### Settings
The plugin includes a Settings model to validate uploads, including:
- The allowed file extensions
- A max filesize limit
- The amount of files per post

### Todo:
- [ ] Add a `onFileDeletion` handler to remove an attached file
