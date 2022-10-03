const g = require('gulp')
exports.build = (cb) => {
    cb();
}

exports.default = (cb) => {
    g.watch(["src/*.json"]).on("change", (p, s) => {
        console.log("Path: ", p);
    });
    cb();
}
