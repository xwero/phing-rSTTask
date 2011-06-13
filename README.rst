==============
Phing rST task
==============

Renders rST (reStructuredText) files into different output formats


Features
========
- renders single files
- render nested filesets
- mappers to generate output file names based on the rst ones
- multiple output formats
- automatically overwrites old files


Missing features
================
- filter chains
- FIXME: update check
- automatically create directories
- custom parameters


Dependencies
============
- *python docutils*

  They contain `rst2html`, `rst2latex`, `rst2man`, `rst2odt`, `rst2s5`,
  `rst2xml`.
