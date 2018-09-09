export default class nxCode {
  // @TODO
  static nxToMarkdown(text) {
    return text;
  }

  // @TODO
  static addUserMentions(text) {
    return text;
  }

  // @TODO
  static addYouTubeEmbed(text) {
    return text;
  }

  // @TODO
  static addSpoilers(text) {
    return text;
  }

  // @TODO
  static addLazyLoadClass(text, placeholder) {
    return text;
  }

  static formatText(text) {
    let formattedText = text;
    formattedText = this.nxToMarkdown(formattedText);
    formattedText = this.addYouTubeEmbed(formattedText);
    formattedText = this.addSpoilers(formattedText);
    formattedText = this.addUserMentions(formattedText);

    return formattedText;
  }
}
