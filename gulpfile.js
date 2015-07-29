//------------------------------------------------------------------------
//  $Required plugins
//------------------------------------------------------------------------


var gulp = require('gulp');
var plumber = require('gulp-plumber');
var gutil = require('gulp-util');
var rename = require('gulp-rename');

// CSS
var sass = require('gulp-sass');
var autoprefix = require('gulp-autoprefixer');
var minifycss = require('gulp-minify-css');
var postcss = require('gulp-postcss');
var opacity = function(css) {
    css.eachDecl(function(decl, i) {
        if (decl.prop === 'opacity') {
            decl.parent.insertAfter(i, {
                prop: '-ms-filter',
                value: '"progid:DXImageTransform.Microsoft.Alpha(Opacity=' + (parseFloat(decl.value) * 100) + ')"'
            });
        }
    });
};

// JS
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var jshint = require('gulp-jshint');

// Other
var newer = require('gulp-newer');
var imagemin = require('gulp-imagemin');
var pngcrush = require('imagemin-pngcrush');


//------------------------------------------------------------------------
//  $Build variables
//------------------------------------------------------------------------


var isProduction = true;

if (gutil.env.dev === true) {
    isProduction = false;
}


//------------------------------------------------------------------------
//  $Paths
//------------------------------------------------------------------------


var dev = 'src/';
var build = '';
var cssSrc;

if (isProduction) {
    cssSrc = dev + 'sass/**/*.scss';
} else {
    cssSrc = ['' + dev + 'sass/**/*.scss','!' + dev + 'sass/**/style-ie8.scss'];
}

var paths = {
    css: {
        src: cssSrc,
        dest: build
    },

    js: {
        src: dev + 'js/**/*js',
        dest: build + 'js/',
        user: {
            src: dev + 'js/*.js'
        },
        plugins: {
            src: dev + 'js/_plugins/*.js'
        },
        head: {
            src: dev + 'js/_header/*.js',
            dest: build + 'js/header/'
        }
    },

    img: {
        src: dev + 'img/**',
        dest: build + 'assets/img/'
    }
};


//------------------------------------------------------------------------
//  $Run Sass, autoprefix, minify
//------------------------------------------------------------------------


gulp.task('sass', function() {
    gulp.src(paths.css.src)
        .pipe(sass({
            errLogToConsole: true
        }))
        .pipe(autoprefix({
            browsers: ['> 1%', 'Firefox > 10', 'ie 8']
        }))
        .pipe(postcss([opacity]))
        .pipe(gulp.dest(paths.css.dest))
        .pipe(isProduction ? minifycss() : gutil.noop())
        .pipe(gulp.dest(paths.css.dest));
});


//------------------------------------------------------------------------
//  $Concatanate and minify main scripts
//------------------------------------------------------------------------


gulp.task('js', function() {
    gulp.src(paths.js.user.src)
        .pipe(plumber())
        .pipe(jshint())
        .pipe(jshint.reporter('default'));

    gulp.src([paths.js.plugins.src, paths.js.user.src])
        .pipe(plumber())
        .pipe(concat('scripts.js'))
        .pipe(gulp.dest(paths.js.dest))
        .pipe(isProduction ? uglify() : gutil.noop())
        .pipe(gulp.dest(paths.js.dest));
});


//------------------------------------------------------------------------
//  $Concat and minify header scripts
//------------------------------------------------------------------------


gulp.task('jsHead', function() {
    gulp.src(paths.js.head.src)
        .pipe(concat('header.js'))
        .pipe(gulp.dest(paths.js.head.dest))
        .pipe(uglify())
        .pipe(gulp.dest(paths.js.head.dest));
});


//------------------------------------------------------------------------
//  $Minify images
//------------------------------------------------------------------------


gulp.task('imagemin', function () {
    gulp.src(paths.img.src)
        .pipe(plumber())
        .pipe(newer(paths.img.dest))
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngcrush()]
        }))
        .pipe(gulp.dest(paths.img.dest));
});


//------------------------------------------------------------------------
//  $Watch task
//------------------------------------------------------------------------


gulp.task('watch', ['sass', 'js', 'jsHead', 'imagemin'] ,function() {
    gulp.watch(paths.css.src, ['sass']);
    gulp.watch(paths.js.src, ['js']);
    gulp.watch(paths.js.head.src, ['jsHead']);
    gulp.watch(paths.img.src, ['imagemin']);
});


//------------------------------------------------------------------------
//  $Default
//------------------------------------------------------------------------


// Default task
gulp.task('default', ['watch']);