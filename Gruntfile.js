module.exports = function( grunt ) {

  'use strict';
  var banner = '/**\n * <%= pkg.homepage %>\n * Copyright (c) <%= grunt.template.today("yyyy") %>\n * This file is generated automatically. Do not edit.\n */\n';
  // Project configuration
  grunt.initConfig( {

    pkg: grunt.file.readJSON( 'package.json' ),

    wp_readme_to_markdown: {
      your_target: {
        files: {
          'README.md': 'readme.txt'
        }
      },
    },
    zip: {
      'location/to/zip/files.zip': ['file/to/zip.js', 'another/file.css']
    },
      copy: {
          main: {
              options: {
                  mode: true
              },
              src: [
                  '**',
                  '*.zip',
                  '!node_modules/**',
                  '!build/**',
                  '!css/sourcemap/**',
                  '!.git/**',
                  '!bin/**',
                  '!.gitlab-ci.yml',
                  '!bin/**',
                  '!tests/**',
                  '!phpunit.xml.dist',
                  '!*.sh',
                  '!*.map',
                  '!Gruntfile.js',
                  '!package.json',
                  '!.gitignore',
                  '!phpunit.xml',
                  '!README.md',
                  '!sass/**',
                  '!codesniffer.ruleset.xml',
                  '!vendor/**',
                  '!composer.json',
                  '!composer.lock',
                  '!package-lock.json',
                  '!phpcs.xml.dist',
              ],
              dest: 'wp-themes-plugins-stats/'
          }
      },
      compress: {
          main: {
              options: {
                  archive: 'wp-themes-plugins-stats.zip',
                  mode: 'zip'
              },
              files: [
                  {
                      src: [
                          './wp-themes-plugins-stats/**'
                      ]

                  }
              ]
          }
      },

      clean: {
          main: ['wp-themes-plugins-stats'],
          zip: ['wp-themes-plugins-stats.zip'],
      },

      makepot: {
          target: {
              options: {
                  domainPath: '/',
                  mainFile: 'wp-wp-themes-plugins-stats.php',
                  potFilename: 'languages/wp-themes-plugins-stats.pot',
                  potHeaders: {
                      poedit: true,
                      'x-poedit-keywordslist': true
                  },
                  type: 'wp-plugin',
                  updateTimestamp: true
              }
          }
      },
      
      addtextdomain: {
          options: {
              textdomain: 'wp-themes-plugins-stats',
          },
          target: {
              files: {
                  src: ['*.php', '**/*.php', '!node_modules/**', '!php-tests/**', '!bin/**', '!admin/bsf-core/**']
              }
          }
      }

  });

  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-compress');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-wp-i18n');
  grunt.loadNpmTasks('grunt-zip');
  
  grunt.registerTask('i18n', ['addtextdomain', 'makepot']);
  grunt.registerTask('release', ['clean:zip', 'copy', 'compress', 'clean:main']);


  grunt.loadNpmTasks('grunt-wp-readme-to-markdown');

  grunt.registerTask('readme', ['wp_readme_to_markdown']);

  grunt.util.linefeed = '\n';

};