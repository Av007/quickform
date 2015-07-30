var gulp = require('gulp');

gulp.task('default', function() {
    var jsFiles = [
        'bower_components/jquery/dist/jquery.js',
        'bower_components/modernizr/modernizr.js',
        'bower_components/foundation/js/foundation.min.js'
    ];
    gulp.src(jsFiles)
        .pipe(gulp.dest('js/vendor'));

    var cssFiles = [
        'bower_components/foundation/css/foundation.min.css',
        'bower_components/foundation/css/foundation.css.map',
        'bower_components/foundation/css/normalize.min.css',
        'bower_components/foundation/css/normalize.css.map',
    ];
    gulp.src(cssFiles)
        .pipe(gulp.dest('css'));
});
