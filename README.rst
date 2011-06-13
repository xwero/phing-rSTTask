==============
Phing rST task
==============

Renders rST (reStructuredText) files into different output formats


Features
========
- renders single files
- render nested filesets
- multiple output formats
- automatically overwrites old files


Missing features
================
- mappers to generate rst output file names based on the rst ones
- filter chains
- FIXME: update check
- automatically create directories
- custom parameters


Dependencies
============
- *python docutils*

  They contain `rst2html`, `rst2latex`, `rst2man`, `rst2odt`, `rst2s5`,
  `rst2xml`.
