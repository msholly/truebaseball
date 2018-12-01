var gulp = require('gulp');
    plugins = require('gulp-load-plugins')(),
    browserSync = require('browser-sync').create(),
    del = require('del'),
    manifest = require('./assets/manifest.json');

var assets = manifest.assets;
var config = manifest.config;

gulp.task('sass', function(){
  return gulp.src(assets.styles)
    .pipe(plugins.plumber({
      errorHandler: plugins.notify.onError("Error: <%= error.message %>")
    }))
    .pipe(plugins.sass()) // Using gulp-sass
    .pipe(plugins.autoprefixer({
      browsers: ['last 2 versions'],
      cascade: false
    }))
    .pipe(plugins.cssnano())
    .pipe(plugins.size())
    .pipe(gulp.dest('dist/styles'))
    .pipe(browserSync.reload({
      stream: true
    }))
});

gulp.task('scripts', function(){
  return gulp.src(assets.scripts)
    .pipe(plugins.concat('main.js'))
    .pipe(plugins.minify({
      ext:{
        min:'.js'
      },
      noSource: true
    }))
    .pipe(plugins.jshint())
    .pipe(plugins.plumber({
      errorHandler: plugins.notify.onError("Error: <%= error.message %>")
    }))
    .pipe(gulp.dest('dist/scripts'))
    .pipe(browserSync.reload({
      stream: true
    }))
});

gulp.task('fonts', function(){
  return gulp.src([
      './assets/fonts/*'
    ])
    .pipe(gulp.dest('dist/fonts'))
    .pipe(browserSync.reload({
      stream: true
    }))
});

gulp.task('clean', function () {
  return del([
    'dist',
  ]);
});

gulp.task('watch', ['sass', 'scripts', 'fonts'], function() {

  browserSync.init({
    open: 'external',
    proxy: "https://" + config.devUrl,
    host: config.devUrl,
    files: "*.php",
    // port: 3000,
    https: true,
    socket: {
      domain: "https://" + config.devUrl
    },
  });

  gulp.watch('./assets/styles/*.scss', ['sass']);
  gulp.watch('./assets/styles/**/*.scss', ['sass']);
  gulp.watch('./assets/scripts/*.js', ['scripts']);
  gulp.watch('./*.php').on('change', browserSync.reload);
});

gulp.task('default', ['sass', 'scripts', 'fonts']);
