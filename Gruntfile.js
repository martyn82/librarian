module.exports = function (grunt) {
    "use strict";

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        files: {
            scripts: {
                angular: [
                    "vendor/bower_components/angular/angular.min.js",
                    "vendor/bower_components/angular-cookies/angular-cookies.min.js",
                    "vendor/bower_components/angular-route/angular-route.min.js"
                ],
                app: {
                    src: [
                        "app/Resources/scripts/services/!(*\.test)*.js",
                        "app/Resources/scripts/controllers/*.js",
                        "app/Resources/scripts/librarian.js",
                        "app/Resources/scripts/config/*.js"
                    ],
                    dest: "web/bundles/app/js/librarian.min.js"
                }
            }
        },

        watch: {
            options: {
                livereload: true
            },
            all: {
                files: [
                    'app/Resources/styles/**.scss',
                    'app/Resources/scripts/**.js',
                    'app/Resources/views/**.html',
                    'app/Resources/views/**.twig'
                ],
                tasks: ['build']
            },
            livereload: {
                files: [
                    'app/Resources/**'
                ]
            }
        },

        clean: {
            assets: [
                'web/bundles/app/assets/**'
            ],
            scripts: [
                'web/bundles/app/js/**'
            ],
            styles: [
                'web/bundles/app/css/**'
            ],
            views: [
                'web/bundles/app/views/**'
            ],
            after: [
                '<%= files.scripts.app.dest %>'
            ]
        },

        copy: {
            assets: {
                cwd: 'app/Resources/assets',
                src: ['**'],
                dest: 'web/bundles/app/',
                expand: true
            },
            views: {
                cwd: 'app/Resources/views',
                src: ['**.html', '**/*.html'],
                dest: 'web/bundles/app/views',
                expand: true
            }
        },

        sass: {
            build: {
                files: {
                    'web/bundles/app/css/default.css': 'app/Resources/styles/main.scss'
                }
            }
        },

        concat: {
            scripts: {
                src: [
                    '<%= files.scripts.angular %>',
                    '<%= files.scripts.app.dest %>'
                ],
                dest: 'web/bundles/app/js/app.js'
            }
        },

        uglify: {
            scripts: {
                options: {
                    mangle: false
                },
                files: {
                    '<%= files.scripts.app.dest %>': '<%= files.scripts.app.src %>'
                }
            }
        },

        cssmin: {
            styles: {
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
        },

        karma: {
            unit: {
                singleRun: true,
                reporters: ['dots', 'coverage'],
                coverageReporter: {
                    type: 'lcov',
                    dir: 'report/test',
                    subdir: '.'
                },
                preprocessors: {
                    'app/Resources/scripts/**/!(*.test).js': ['coverage']
                },
                port: 9876,
                runnerPort: 9100,
                colors: true,
                captureTimeout: 5000,
                reportSlowerThan: 30,
                plugins: ['karma-*'],
                browsers: ['PhantomJS'],
                files: {
                    src: [
                        "vendor/bower_components/angular/angular.min.js",
                        "vendor/bower_components/angular-cookies/angular-cookies.min.js",
                        "vendor/bower_components/angular-route/angular-route.min.js",
                        "vendor/bower_components/angular-mocks/angular-mocks.js",
                        "app/Resources/scripts/services/*.js"
                    ]
                },
                frameworks: ['qunit']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-karma');
    grunt.loadNpmTasks('grunt-sass');

    grunt.registerTask('default', ['build']);
    grunt.registerTask('build', ['build:styles', 'build:scripts', 'build:copy', 'clean:after']);
    grunt.registerTask('build:styles', ['clean:styles', 'sass:build', 'cssmin:styles']);
    grunt.registerTask('build:scripts', ['clean:scripts', 'uglify:scripts', 'concat:scripts']);
    grunt.registerTask('build:copy', ['clean:assets', 'clean:views', 'copy:assets', 'copy:views']);
};
