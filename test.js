function StaticRange() {const { assert } = require('chai');

assert.equal(2 + 2, 4);  // This passes silently

console.log(typeof assert); // Output: 'function'
console.log(assert);};

StaticRange();


// Logs the actual `assert` function (object with many methods)
st