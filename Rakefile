require 'packr'

desc "Minify Tarski's JavaScript files"
task :minify do
  Dir.glob("app/js/*.dev.js").each do |file|
    code       = File.read(file)
    compressed = Packr.pack(code)
    File.open(file.sub(/\.dev.js$/, ".js"), 'wb') { |f| f.write(compressed) }
  end
end
