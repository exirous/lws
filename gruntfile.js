module.exports = function (grunt)
{
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            css: {
                src: [
                    "css/bootstrap/bootstrap.css",
                    "css/style.css",
                    "css/select2.css",
                    "css/select2-bootstrap.css",
                    "css/loading-bar.css",
                    "scripts/sceditor/minified/themes/default.min.css"
                ],
                dest: 'style.css'
            },
            css_new: {
                src: [
                    "css/bootstrap/bootstrap.css",
                    "css/style_new.css",
                    "css/select2.css",
                    "css/select2-bootstrap.css",
                    "css/loading-bar.css",
                    "scripts/sceditor/minified/themes/default.min.css"
                ],
                dest: 'style_new.css'
            },
            js: {
                src: [
                    "scripts/jquery.js",
                    "scripts/lib/select2.min.js",
                    "scripts/lib/angular.min.js",
                    "scripts/lib/angular-ui-router.js",
                    "scripts/lib/statehelper.js",
                    "scripts/lib/angular-resource.js",
                    "scripts/lib/angular-sanitize.js",
                    "scripts/lib/animate.js",
                    "scripts/lib/select2.js",
                    "scripts/lib/ui-bootstrap-tpls.js",
                    "scripts/lib/dialogs.js",
                    "scripts/app.js",
                    "scripts/services/services.js",
                    "scripts/filters/filters.js",
                    "scripts/directives/directives.js",
                    "scripts/controllers/controllers.js",
                    //"scripts/lib/socket.io.js",
                    "scripts/loading-bar.js",
                    "scripts/lib/angular-file-upload.js",
                    "scripts/lib/angular-dnd.js",
                    "scripts/sceditor/minified/jquery.sceditor.bbcode.min.js"
                ],
                dest: 'scripts.js'
            }
        },

        /*


        cssmin: {
            css: {
                src: 'style.css',
                dest: 'style.css'
            }
        },
        uglify: {
            options: {
                mangle: false
            },
            js: {
                files: {
                    'scripts.js': ['scripts.js']
                }
            }
        },*/
        watch: {
            files: [
                "scripts/jquery.js",
                "scripts/lib/select2.min.js",
                "scripts/lib/angular.min.js",
                "scripts/lib/angular-ui-router.js",
                "scripts/lib/statehelper.js",
                "scripts/lib/angular-resource.js",
                "scripts/lib/angular-sanitize.js",
                "scripts/lib/animate.js",
                "scripts/lib/select2.js",
                "scripts/lib/ui-bootstrap-tpls.js",
                "scripts/lib/dialogs.js",
                "scripts/app.js",
                "scripts/services/services.js",
                "scripts/filters/filters.js",
                "scripts/directives/directives.js",
                "scripts/controllers/controllers.js",
                "scripts/lib/socket.io.js",
                "scripts/loading-bar.js",
                "scripts/lib/angular-file-upload.js",
                "scripts/lib/angular-dnd.js",
                "scripts/sceditor/minified/jquery.sceditor.bbcode.min.js",
                "css/bootstrap/bootstrap.css",
                "css/style.css",
                "css/style_new.css",
                "css/select2.css",
                "css/select2-bootstrap.css",
                "css/loading-bar.css",
                "scripts/sceditor/minified/themes/default.min.css"
            ],
            tasks: ['concat'/*, 'cssmin', 'uglify'*/]
        }
    });
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.registerTask('default', ['concat:css','concat:css_new',/* 'cssmin:css',*/ 'concat:js'/*, 'uglify:js'*/]);
};