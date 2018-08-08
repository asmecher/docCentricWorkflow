docCentricWorkflow
==================

CAVEAT: This plugin is EXPERIMENTAL and not intended for production use. It
will not be supported in the same way as production-ready PKP software!
Contributions and suggestions are welcome.

This plugin adds the ability to engage in direct discussions on a submission
document, without the need to view/edit it outside the journal website.

It uses a separate (server-side) tool to convert uploaded documents into the
PDF format. Unoconv (https://github.com/dagwieers/unoconv) is recommended.
Note that this may not be available on many server environments.

The conversion tool should be configured in `config.inc.php` as follows:

```
[cli]

pdf_converter = /usr/bin/unoconv -o {$targetPath} {$sourceFilePath}
```
