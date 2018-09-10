import nxcode from "../../../resources/assets/js/nxcode";
var assert = require("assert");

describe("nxCode", () => {
  describe("addSpoilers", () => {
    it("spoiler tags should be replaced with markup with single tag", () => {
        let text = 'Oh my [spoiler-]Brad Pitt is Edward Norton![-spoiler]';
        let expectedText = 'Oh my <span class="spoiler">Brad Pitt is Edward Norton!</span>';

        const result = nxcode.addSpoilers(text);
        assert.equal(result, expectedText);
    });
  });
});