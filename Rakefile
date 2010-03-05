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
      File.open(file.sub(/\.dev.js$/, ".js"), 'wb') { |f| f.write(compressed) }
    end
  end
  
  desc "Compress CSS files"
  task :css do
    file       = "style.css"
    compressor = YUI::CssCompressor.new
    code       = File.read(file)
    header     = code.match(/\/\*.+?\*\//m)[0]
    compressed = compressor.compress(code)
    
    File.open("style.min.css", "wb") do |f|
      f.write(header + "\n" + compressed)
    end
  end
  
end
