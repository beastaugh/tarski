require 'packr'
require 'yui/compressor'

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
    files      = Dir.glob("{styles,library/css}/*.dev.css") << main
    compressor = YUI::CssCompressor.new
    
    files.each do |file|
      code       = File.read(file)
      compressed = compressor.compress(code)
      
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
