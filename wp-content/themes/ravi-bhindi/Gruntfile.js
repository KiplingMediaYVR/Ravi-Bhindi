'use strict';

module.exports = function (grunt) {

    const sass = require('node-sass');

    require('time-grunt')(grunt);
    require('jit-grunt')(grunt);

    var config = require('./Gruntconfig.js');

    grunt.initConfig({

        config: config,

        newer: {
            options: {
                override: function (details, include) {
                    include(true);
                }
            }
        },

        clean: [
            '<%= config.dist %>',
            '<%= config.assets %>/css',
            '<%= config.assets %>/images',
            '.sass-cache',
            '.tmp'
        ],

        jshint: {
            frontend: {
                files: [{
                    src: '<%= config.source%>/scripts/frontend.js'
                }]
            }
        },

        uglify: {
            my_target: {
                files: {
                    '<%= config.assets %>/scripts/frontend.min.js': [
                        '<%= config.source%>/vendor/bootstrap.min.js',
                        '<%= config.source%>/vendor/slick.js',
                        '<%= config.source%>/vendor/ekko-lightbox.js',
                        '<%= config.source%>/scripts/frontend.js'
                    ]
                }
            }
        },

        cssmin: {
            options: {
                mergeIntoShorthands: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    '<%= config.assets%>/css/frontend.min.css': [
                        '<%= config.source%>/vendor/ekko-lightbox.css',
                        '<%= config.assets%>/css/main.css'
                    ]
                }
            }
        },

        postcss: {
            options: {
                map: true,

                processors: [
                    require('pixrem')(),
                    require('autoprefixer')({browsers: 'last 2 versions'}),
                    // require('cssnano')()
                ]
            },
            dist: {
                src: '<%= config.assets%>/css/main.css'
            }
        },

        sass: {
            options: {
                implementation: sass,
                sourceMap: true
            },
            dist: {
                files: {
                    '<%= config.assets%>/css/main.css': '<%= config.source%>/sass/main.scss'
                }
            }
        },

        sass_globbing: {
            main: {
                files: {
                    //Globbing all the SCSS files -- Just create new files inside these folders and let Grunt do the hard work :)
                    '<%= config.source%>/sass/_libs.scss': '<%= config.source%>/sass/libs/**/*.scss',
                    '<%= config.source%>/sass/_modules.scss': '<%= config.source%>/sass/modules/**/*.scss',
                    '<%= config.source%>/sass/_components.scss': '<%= config.source%>/sass/components/**/*.scss',
                    '<%= config.source%>/sass/_views.scss': '<%= config.source%>/sass/views/**/*.scss'
                },
                options: {
                    useSingleQuotes: false
                }
            }
        },

        imagemin: {

            jpgDist: {
                options: {
                    optimizationLevel: 1,
                    progressive: true
                },

                files: [
                    {
                        expand: true,
                        cwd: '<%= config.source%>/images',
                        src: ['**/*.{jpg,jpeg,gif}'],
                        dest: '<%= config.assets%>/images'
                    }
                ]
            },
            pngDist: {
                options: {
                    optimizationLevel: 1
                },

                files: [
                    {
                        expand: true,
                        cwd: '<%= config.source%>/images',
                        src: ['**/*.png'],
                        dest: '<%= config.assets%>/images'
                    }
                ]
            }
        },

        svgmin: {
            options: {
                plugins: [
                    {removeViewBox: false},
                    {removeUselessStrokeAndFill: false}
                ]
            },
            dist: {
                expand: true,
                cwd: '<%= config.source%>/images/svg',
                src: ['*.svg'],
                dest: '<%= config.assets%>/images/svg'
            }
        },

        concurrent: {
            first: [
                'sass_globbing:main'
            ],
            second: [
                'uglify',
                'cssmin'
            ],
            imagesDist: [
                'imagemin:jpgDist',
                'imagemin:pngDist',
                'svgmin'
            ]
        },

        copy: {
            dist: {
                files: [
                    {
                        expand: true,
                        src: [
                            './*.{html,php}',
                            './*.css'
                        ],
                        dest: '<%= config.dist %>/',
                        filter: 'isFile'
                    },

                    {expand: true, src: ['./<%= config.assets%>/images/**/*'], dest: '<%= config.dist %>'},

                    {expand: true, src: ['./<%= config.assets%>/scripts/**/*'], dest: '<%= config.dist %>'},

                    {expand: true, src: ['./<%= config.assets%>/css/**/*'], dest: '<%= config.dist %>'},

                    {expand: true, src: ['./<%= config.assets%>/fonts/**/*'], dest: '<%= config.dist %>'},

                    {expand: true, src: ['./<%= config.assets%>/vendor/**/*'], dest: '<%= config.dist %>'}
                ]
            }
        },

        watch: {
            options: {
                livereload: true
            },

            scripts: {
                files: ['<%= config.source%>/scripts/frontend.js'],
                tasks: ['jshint:frontend', 'uglify'],
                options: {
                    spawn: false
                }
            },

            sass: {
                files: ['<%= config.source%>/sass/**/*.scss'],
                tasks: ['newer:sass', 'cssmin'],
                options: {
                    spawn: false
                }
            },

            images: {
                files: ['<%= config.source%>/images/**/*.{png,jpg,gif,svg}'],
                tasks: ['newer:imagemin:jpgDev', 'newer:imagemin:pngDev', 'newer:svgmin'],
                options: {
                    spawn: false
                }
            },

            html: {
                files: ['./**/*.{html,php}'],
                tasks: [],
                options: {
                    spawn: false
                }
            }
        }
    });

    grunt.registerTask('build', [
        'clean',
        'jshint:frontend',
        'concurrent:first',
        'sass',
        'postcss',
        'concurrent:second',
        'concurrent:imagesDist',
        'copy:dist'
    ]);

    grunt.registerTask('default', [
        'build',
        'watch'
    ]);
};