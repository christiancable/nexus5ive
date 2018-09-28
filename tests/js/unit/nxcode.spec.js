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

  describe("addUserMentions", function() {
    fixtures = {
      addUserMentions: [
        {
          info: "blank post",
          input: "",
          expected: ""
        },
        {
          info: "single mention",
          input: "hey @christiancable how are you?",
          expected:
            'hey <span class="text-muted">@</span><mark><strong><a href="/users/christiancable">christiancable</a></strong></mark> how are you?'
        },
        {
          info: "multiple mentions",
          input: "hey @christiancable have you seen @AgentOrange",
          expected:
            'hey <span class="text-muted">@</span><mark><strong><a href="/users/christiancable">christiancable</a></strong></mark> have you seen <span class="text-muted">@</span><mark><strong><a href="/users/AgentOrange">AgentOrange</a></strong></mark>'
        },
        {
          info: "mention with html",
          input: "<p>@christiancable</p>",
          expected:
            '<p><span class="text-muted">@</span><mark><strong><a href="/users/christiancable">christiancable</a></strong></mark></p>'
        }
      ]
    };

    fixtures.addUserMentions.map(function(test) {
      it(test.info, function() {
        let text = test.input;
        let expectedText = test.expected;
        const result = nxcode.addUserMentions(text);
        assert.equal(result, expectedText);
      });
    });
  });

  describe("nxToMarkdown", function() {
    fixtures = {
      nxToMarkdown: [
        {
          info: "blank post",
          input: "",
          expected: ""
        },
        {
          info: "nxcode becomes markdown",
          input: "german [b-]bold[-b] [i-]italic[-i]",
          expected: "german __bold__ _italic_"
        },
        {
          info: "picture tags are converted",
          input: "[picture-]xiondion.jpg[-picture]",
          expected: "![image](xiondion.jpg)"
        },
        {
          info: "unknown tags are ignored",
          input: "[hudson-]Yo![-hudson]",
          expected: "[hudson-]Yo![-hudson]"
        }
      ]
    };

    fixtures.nxToMarkdown.map(function(test) {
      it(test.info, function() {
        let text = test.input;
        let expectedText = test.expected;
        const result = nxcode.nxToMarkdown(text);
        assert.equal(result, expectedText);
      });
    });
  });

  describe("addYouTubeEmbed", function() {
    fixtures = {
      addYouTubeEmbed: [
        {
          info: "blank post",
          input: "",
          expected: ""
        },
        {
          info: "single valid youtube tag",
          input:
            "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
          expected: `<div class="video-wrapper">
    <iframe id="youtube-player" src="//www.youtube.com/embed/dQw4w9WgXcQ?rel=0&showinfo=0&autohide=1" frameborder="0" allowfullscreen></iframe>
</div>`
        },
        {
          info: "youtube tag with invalid content is ignored",
          input: "https://vimeo.com/87031388",
          expected: "https://vimeo.com/87031388"
        },
        {
          info: "Red Hot Chili Peppers - Give It Away - ID with an underscore",
          input: "https://youtu.be/Mr_uHJPUlO8",
          expected: `<div class="video-wrapper">
    <iframe id="youtube-player" src="//www.youtube.com/embed/Mr_uHJPUlO8?rel=0&showinfo=0&autohide=1" frameborder="0" allowfullscreen></iframe>
</div>`
        }
      ]
    };

    fixtures.addYouTubeEmbed.map(function(test) {
      it(test.info, function() {
        let text = test.input;
        let expectedText = test.expected;
        const result = nxcode.addYouTubeEmbed(text);
        assert.equal(result, expectedText);
      });
    });
  });
});
