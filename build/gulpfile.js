var gulp = require('gulp');

var extension = require('./package.json');
var config    = require('./gulp-config.json');

var argv       = require('yargs').argv;
var fs         = require('fs');
var requireDir = require('require-dir');
var zip        = require('gulp-zip');
var xml2js     = require('xml2js');
var parser     = new xml2js.Parser();

var jgulp = requireDir('./node_modules/joomla-gulp', {recurse: true});
var dir = requireDir('./joomla-gulp-extensions', {recurse: true});

// Override of the release script
gulp.task('release', function (cb) {
	fs.readFile( '../pkg_reddebug.xml', function(err, data) {
		parser.parseString(data, function (err, result) {
			var version = result.extension.version[0];

			var fileName = argv.skipVersion ? extension.name + '.zip' : extension.name + '-v' + version + '.zip';

			return gulp.src([
					'../extensions/**/*',
					'../*(LICENSE|pkg_reddebug.xml)'
				],{ base: '../' })
				.pipe(zip(fileName))
				.pipe(gulp.dest('releases'))
				.on('end', cb);
		});
	});
});
