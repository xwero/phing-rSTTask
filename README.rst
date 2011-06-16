==============
Phing rST task
==============

Renders rST (reStructuredText) files into different output formats.

Homepage: https://gitorious.org/phing/rsttask

.. contents::

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


License
=======
The Phing rSTTask is licensed under the `LGPLv3 or later`__.

__ http://www.gnu.org/licenses/lgpl.html


Installation
============
As long as the rSTTask is not distributed with phing, you need to make
Phing aware of it.

First, clone the Git repository: ::

 $ git clone git://gitorious.org/phing/rsttask.git
 $ cd rsttask
 $ phing

Phing will render this ``README.rst`` into ``README.html`` and run
the unit tests.


Usage
=====

Now, in your own ``build.xml`` file do the following ::

 <?xml version="1.0" encoding="utf-8"?>
 <project name="test" basedir="." default="test.single.all">
   <includepath classpath="/path-to-git-checkout/src/" />
   <taskdef name="rST" classname="phing.tasks.ext.rSTTask" />
   ...


Examples
========

Render a single rST file to HTML
--------------------------------
By default, HTML is generated. If no target file is specified,
the input file name is taken, and its extension replaced with
the correct one for the output format. ::

 <?xml version="1.0" encoding="utf-8"?>
 <project name="example" basedir="." default="single">
   <target name="single" description="render a single rST file to HTML">

     <rST file="path/to/file.rst" />

   </target>
 </project>


Render a single rST file to any supported format
------------------------------------------------
The ``format`` attribute determines the output format: ::

 <?xml version="1.0" encoding="utf-8"?>
 <project name="example" basedir="." default="single">
   <target name="single" description="render a single rST file to S5 HTML">

     <rST file="path/to/file.rst" format="s5" />

   </target>
 </project>


Specifying the output file name
-------------------------------
::

 <?xml version="1.0" encoding="utf-8"?>
 <project name="example" basedir="." default="single">
   <target name="single" description="render a single rST file">

     <rST file="path/to/file.rst" targetfile="path/to/output/file.html" />

   </target>
 </project>


Rendering multiple files
------------------------
A nested ``fileset`` tag may be used to specify multiple files. ::

 <?xml version="1.0" encoding="utf-8"?>
 <project name="example" basedir="." default="multiple">
   <target name="multiple" description="renders several rST files">

     <rST>
      <fileset dir=".">
        <include name="README.rst" />
        <include name="docs/\*.rst" />
      </fileset>
     </rST>

   </target>
 </project>


Rendering multiple files to another directory
---------------------------------------------
A nested ``mapper`` may be used to determine the output file names. ::

 <?xml version="1.0" encoding="utf-8"?>
 <project name="example" basedir="." default="multiple">
   <target name="multiple" description="renders several rST files">

     <rST>
      <fileset dir=".">
        <include name="README.rst" />
        <include name="docs/\*.rst" />
      </fileset>
      <mapper type="glob" from="\*.rst" to="path/to/my/\*.xhtml"/>
     </rST>

   </target>
 </project>


Modifying files after rendering
-------------------------------
You may have variables in your rST code that can be replaced
after rendering, i.e. the version of your software. ::

 <?xml version="1.0" encoding="utf-8"?>
 <project name="example" basedir="." default="filterchain">
   <target name="filterchain" description="renders several rST files">

     <rST>
      <fileset dir=".">
        <include name="README.rst" />
        <include name="docs/\*.rst" />
      </fileset>
      <filterchain>
        <replacetokens begintoken="##" endtoken="##">
          <token key="VERSION" value="1.23.0" />
        </replacetokens>
      </filterchain>
     </rST>

   </target>
 </project>



Rendering changed files only
----------------------------
The ``uptodate`` attribute determines if only those files should
be rendered that are newer than their output file. ::

 <?xml version="1.0" encoding="utf-8"?>
 <project name="example" basedir="." default="multiple">
   <target name="multiple" description="renders several rST files">

     <rST uptodate="true">
      <fileset dir=".">
        <include name="docs/\*.rst" />
      </fileset>
     </rST>

   </target>
 </project>


Specify a custom CSS file
-------------------------
You may pass any additional parameters to the rst conversion tools
with the ``toolparam`` attribute. ::

 <?xml version="1.0" encoding="utf-8"?>
 <project name="example" basedir="." default="single">
   <target name="single" description="render a single rST file to S5 HTML">

     <rST file="path/to/file.rst" toolparam="--stylesheet-path=custom.css" />

   </target>
 </project>


