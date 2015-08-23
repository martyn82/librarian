QUnit.module('QueryParser', {});

QUnit.test('Parse parses query string and returns object with key-value pairs.', function (assert) {
    var queryString = 'foo=bar&baz=boo&bar';
    var expectedParams = {
        foo: 'bar',
        baz: 'boo',
        bar: undefined
    };

    var parser = new QueryParser();
    var actual = parser.parse(queryString);
    assert.propEqual(actual, expectedParams);
});

QUnit.test('Parse without query string returns empty object', function (assert) {
    var parser = new QueryParser();
    var actual = parser.parse(null);

    var propertyCount = 0;
    for (var k in actual) {
        if (k !== '' && actual.hasOwnProperty(k)) {
            propertyCount++;
        }
    }

    assert.equal(propertyCount, 0);
});
