Plugin Copy Build
=================

Copies all files from your build, with the exception of those in the "ignore" build settings property, to a directory 
of your choosing.

Configuration
-------------

### Options

* **directory** [string, required] - The directory to which you want to copy the build.
* **respect_ignore** [bool, optional] - Whether to respect the global "ignore" setting when copying files. Default is false.
* **wipe** [bool, optional] - Set true if you want destination directory to be cleared before copying. Default is false.
