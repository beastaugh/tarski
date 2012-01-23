require 'pathname'

require 'packr'
require 'sass'

desc "Create a zip file of the lastest release"
task :build do
  src_dir     = Pathname.new(File.dirname(__FILE__)).expand_path
  build_dir   = src_dir + "tarski"
  build_files = [".git", ".gitignore", ".DS_Store", "Rakefile", "tarski"]
  zip_name    = "tarski_#{theme_version(src_dir + "style.dev.css")}.zip"
  
  # Create build directory
  FileUtils.rm_rf build_dir if Dir.exist? build_dir
  FileUtils.mkdir build_dir
  
  # Copy files
  (Dir.entries(src_dir) - build_files - [".", ".."]).
  reject {|path| path =~ /\.zip$/ }.each do |path|
    FileUtils.cp_r path, build_dir + path
  end
  
  # Zip build directory
  `cd #{src_dir}; zip -rmq #{zip_name} tarski`
end

desc "Compress JavaScript and CSS files"
task :minify => [:"minify:js", :"minify:css"]

namespace :minify do
  
  desc "Compress JavaScript files"
  task :js do
    Dir.glob("app/js/*.dev.js").each do |file|
      options    = {:shrink_vars => true, :private => true}
      code       = File.read(file)
      compressed = Packr.pack(code, options)
      File.open(file.sub(/\.dev.js$/, ".js"), "w") { |f| f.write(compressed) }
    end
  end
  
  desc "Compress CSS files"
  task :css do
    main       = "style.dev.css"
    files      = Dir.glob("library/css/*.dev.css") << main
    
    files.each do |file|
      code       = File.read(file)
      compressed = minify_css(code, file)
      
      if file == main
        header = code.match(/\/\*.+?\*\//m)[0]
        output = header + "\n" + compressed
      else
        output = compressed
      end
      
      File.open(file.sub(/\.dev\.css/, ".css"), "w") { |f| f.write(output) }
    end
  end
  
end

def theme_version(stylesheet)
  lines  = File.read(stylesheet).split("\n")
  prefix = "Version: "
  lines.select {|line| line =~ /^#{prefix}/ }.first.sub(prefix, "").strip
end

def minify_css(str, filename)
  root_node = ::Sass::SCSS::CssParser.new(str, filename).parse
  root_node.options = {:style => :compressed}
  root_node.render.strip
end
