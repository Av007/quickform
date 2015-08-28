var gulp = require('gulp');

gulp.task('default', function() {
    var jsFiles = [
        'bower_components/jquery/dist/jquery.min.js',
        'bower_components/modernizr/modernizr.js',
        'bower_components/foundation/js/foundation.min.js',
        'bower_components/angularjs/angular.min.js',
        'bower_components/angularjs/angular.min.js.map',
        'bower_components/jquery.maskedinput/dist/jquery.maskedinput.min.js'
    ];
    gulp.src(jsFiles)
        .pipe(gulp.dest('js/vendor'));

    var cssFiles = [
        'bower_components/foundation-icon-fonts/foundation-icons.ttf',
        'bower_components/foundation-icon-fonts/foundation-icons.woff',
        'bower_components/foundation/css/foundation.min.css',
        'bower_components/foundation/css/foundation.css.map',
        'bower_components/foundation/css/normalize.min.css',
        'bower_components/foundation/css/normalize.css.map',
        'bower_components/angularjs/angular-csp.css',
        'bower_components/foundation-icon-fonts/foundation-icons.css'
    ];
    gulp.src(cssFiles)
        .pipe(gulp.dest('css'));
});
