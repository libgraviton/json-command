var args = process.argv.slice(2);
var result = args.map(function (arg) {
    arg = JSON.parse(arg);
    return {
        key: arg.key,
        result: Math.random()
    };
});

console.log(JSON.stringify(result, null, 2));