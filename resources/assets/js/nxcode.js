// @TODO
// 1. tests
// 2. implementation
// 3. removal of nxcode route

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

  // https://regex101.com/r/8hiMAA/2
  static addSpoilers(text) 
  {
    const regex = /(?:\[spoiler-\])(.*?)(?:\[-spoiler])/gmi;
    const subst = `<span class="spoiler">$1</span>`;
    const result = text.replace(regex, subst);

    return result;
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
