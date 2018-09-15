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
  static addSpoilers(text) {
    const regex = /(?:\[spoiler-\])(.*?)(?:\[-spoiler])/gim;
    const subst = `<span class="spoiler">$1</span>`;
    const result = text.replace(regex, subst);

    return result;
  }

  // https://regex101.com/r/YzSZVi/2
  static addLazyLoadClass(text, lazyclass, placeholder) {
    const regex = /<img src="(.*?)"/gm;
    const subst =
      `<img class="` + lazyclass + `" src="` + placeholder + `" data-src="$1"`;
    const result = text.replace(regex, subst);

    return result;
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
