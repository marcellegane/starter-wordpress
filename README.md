#Starter

A front-end project starter.

##Features

* [Gulp](http://gulpjs.com/)
    * Compile, autoprefix and minify Sass
    * Error check, concatanate and minify Javascript
    * Optimise images

##Installation

Install Gulp globally

    npm install -g gulp

Then install the required Gulp plugins as specified in `gulpfile.js`

    npm install

##Usage

The project is built around a file structure whereby all development images, Sass and Javascript are housed in the `src/` directory. Gulp then runs tasks on these files and outputs the optimised code to the `build/` directory. Take a look at the `$Paths` section of gulpfile.js for details.

To start the Gulp task simply enter `gulp` on the command line. To run in development mode, whereby files are compiled but not minified, run gulp with the `--dev` flag.

    gulp --dev
