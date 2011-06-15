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
- filter chains to e.g. replace variables after rendering
- custom parameters to the rst2* tool
- configurable rst tool path
- uptodate check
- automatically overwrites old files
- automatically creates target directories


Attributes
==========

============== ======== =========================== ========== ========
Name           Type     Description                 Default    Required
============== ======== =========================== ========== ========
``file``       String   rST input file to render    n/a        Yes (or fileset)
``format``     String   Output format:              ``html``   No

                        - ``html``
                        - ``latex``
                        - ``man``
                        - ``odt``
                        - ``s5``
                        - ``xml``
``targetfile`` String   Path to store the rendered  magically  No
                        file to                     determined
                                                    from
                                                    input file
``uptodate``   Boolean  Only render if the input    ``false``  No
                        file is newer than the
                        target file
``toolpath``   String   Path to the rst2* tool      determined No
                                                    from
                                                    ``format``
``toolparam``  String   Additional commandline      n/a        No
                        parameters to the rst2*
                        tool
============== ======== =========================== ========== ========


Supported nested tags
=====================
- Fileset
- Mapper
- Filterchain


Dependencies
============
- *python docutils*

  They contain `rst2html`, `rst2latex`, `rst2man`, `rst2odt`, `rst2s5`,
  `rst2xml`.
