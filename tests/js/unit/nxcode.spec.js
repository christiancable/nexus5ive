import nxcode from "../../../resources/assets/js/nxcode";
var assert = require("assert");

describe("nxCode", function() {
  var fixtures;
  const lazyLoadClass = "b-lazy";
  const lazyLoadPlaceholder = "placeholder.jpg";

  describe("addSpoilers", function() {
    fixtures = {
      addSpoilers: [
        {
          info: "replaces spoiler tags with spoiler classes for one spoiler",
          input: "Oh my [spoiler-]Brad Pitt is Edward Norton![-spoiler]",
          expected:
            'Oh my <span class="spoiler">Brad Pitt is Edward Norton!</span>'
        },
        {
          info: "replaces spoiler tags with spoiler classes for many spoilers",
          input:
            "Oh my [spoiler-]Brad Pitt is Edward Norton![-spoiler] and [spoiler-]it was Earth all along[-spoiler]",
          expected:
            'Oh my <span class="spoiler">Brad Pitt is Edward Norton!</span> and <span class="spoiler">it was Earth all along</span>'
        },
        {
          info: "does not change text with no spoiler tags",
          input: "Darth Vader is Luke's father!!",
          expected: "Darth Vader is Luke's father!!"
        }
      ]
    };

    fixtures.addSpoilers.map(function(test) {
      it(test.info, function() {
        let text = test.input;
        let expectedText = test.expected;
        const result = nxcode.addSpoilers(text);
        assert.equal(result, expectedText);
      });
    });
  });

  describe("addLazyLoadClass", function() {
    fixtures = {
      addLazyLoadClass: [
        {
          info: "adds class to single image tag",
          input:
            '<img src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank" />',
          expected:
            '<img class="b-lazy" src="placeholder.jpg" data-src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank" />'
        },
        {
          info: "adds class to multiple image tags",
          input:
            '<img src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank"/> and then this happened <img src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank"/>',
          expected:
            '<img class="b-lazy" src="placeholder.jpg" data-src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank"/> and then this happened <img class="b-lazy" src="placeholder.jpg" data-src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank"/>'
        },
        {
          info: "does not change text with no image tags",
          input: "Here is a pretty picture",
          expected: "Here is a pretty picture"
        }
      ]
    };

    fixtures.addLazyLoadClass.map(function(test) {
      it(test.info, function() {
        let text = test.input;
        let expectedText = test.expected;
        const result = nxcode.addLazyLoadClass(
          text,
          "b-lazy",
          "placeholder.jpg"
        );
        assert.equal(result, expectedText);
      });
    });
  });
});
