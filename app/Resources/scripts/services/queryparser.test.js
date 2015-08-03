QUnit.module('QueryParser', {});

QUnit.test('Parse parses query string and returns object with key-value pairs.', function (assert) {
    var queryString = 'foo=bar&baz=boo&bar';
    var expectedParams = {
        foo: 'bar',
        baz: 'boo',
        bar: undefined
    };

    var parser = new QueryParser();
    actual = parser.parse(queryString);
    assert.deepEqual(expectedParams, actual);
});
