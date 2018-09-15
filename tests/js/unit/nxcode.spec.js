import nxcode from "../../../resources/assets/js/nxcode";
var assert = require("assert");


describe("nxCode", function() {
    
    var fixtures;

    describe("addSpoilers", function() {
        
        fixtures = {
            "addSpoilers": [
                {
                    "info": "replaces spoiler tags with spoiler classes for one spoiler",
                    "input": "Oh my [spoiler-]Brad Pitt is Edward Norton![-spoiler]",
                    "expected": "Oh my <span class=\"spoiler\">Brad Pitt is Edward Norton!</span>"
                },
                {
                    "info": "replaces spoiler tags with spoiler classes for many spoilers",
                    "input": "Oh my [spoiler-]Brad Pitt is Edward Norton![-spoiler] and [spoiler-]it was Earth all along[-spoiler]",
                    "expected": "Oh my <span class=\"spoiler\">Brad Pitt is Edward Norton!</span> and <span class=\"spoiler\">it was Earth all along</span>"
                },
                {
                    "info": "does not change text with no spoiler tags",
                    "input": "Darth Vader is Luke's father!!",
                    "expected": "Darth Vader is Luke's father!!"
                }
            ]
        }  

      fixtures.addSpoilers.map (function(test) {
          it(test.info, function() {
              let text = test.input;
              let expectedText = test.expected;              
              const result = nxcode.addSpoilers(text);
              assert.equal(result, expectedText);
            });
        });
    });
});

