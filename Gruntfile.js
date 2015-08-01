module.exports = function (grunt) {
    "use strict";

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        watch: {
            options: {
                livereload: true
            },
            sass: {
                files: [
                    'app/Resources/styles/**.scss'
                ],
                tasks: ['dist']
            },
            livereload: {
                files: [
                    'app/Resources/**'
                ]
            }
        },

        sass: {
            build: {
                files: {
                    'web/bundles/app/css/default.css': 'app/Resources/styles/main.scss'
                }
            }
        },

        copy: {
            assets: {
                cwd: 'app/Resources/assets',
                src: ['**'],
                dest: 'web/bundles/app/',
                expand: true
            }
        },

        cssmin: {
            dist: {
                files: [
                    {
                        expand: true,
                        cwd: 'web/bundles/app/css',
                        src: ['default.css'],
                        dest: 'web/bundles/app/css',
                        ext: '.min.css'
                    }
                ]
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-sass');

    grunt.registerTask('default', ['build']);
    grunt.registerTask('build', ['sass:build', 'cssmin:dist', 'copy:assets']);
};
