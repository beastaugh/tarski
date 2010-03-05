require 'packr'

desc "Minify Tarski's JavaScript files"
task :minify do
  Dir.glob("app/js/*.js").each do |file|
    next if file =~ /-min\.js$/
    
    code       = File.read(file)
    compressed = Packr.pack(code)
    File.open(file.sub(/\.js$/, "-min.js"), 'wb') { |f| f.write(compressed) }
  end
end
