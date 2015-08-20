var chai = require('chai');
var expect = chai.expect;

module.exports = function () {

    this.Given(/^I am not logged in$/, function (next) {
        browser.get('/').then(function () {
            browser.waitForAngular().then(function () {
                element(by.css('#login-with-github')).isPresent().then(function (value) {
                    expect(value).to.be.true;
                    next();
                });
            });
        });
    });

    this.When(/^I log in using GitHub$/, function (next) {
        browser.manage().addCookie('auth_access_token', 'abcdef1234').then(function () {
            next();
        });
    });

    this.Then(/^I am logged in$/, function (done) {
        browser.get('/').then(function () {
            browser.waitForAngular().then(function () {
                element(by.css('#login-with-github')).isPresent().then(function (value) {
                    expect(value).to.be.false;
                    done();
                });
            });
        });
    });
};