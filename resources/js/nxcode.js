export default class nxCode {
  // this replaces nx code tags with the nearest markdown tag
  static nxToMarkdown(text) {
    const Nx2MD = [
      {
        nx: /\[www-\]/gi,
        md: ""
      },
      {
        nx: /\[-www\]/gi,
        md: ""
      },
      {
        nx: /\[i-\]/gi,
        md: "_"
      },
      {
        nx: /\[-i\]/gi,
        md: "_"
      },
      {
        nx: /\[b-\]/gi,
        md: "__"
      },
      {
        nx: /\[-b\]/gi,
        md: "__"
      },
      {
        nx: /\[picture-\]/gi,
        md: "![image]("
      },
      {
        nx: /\[-picture\]/gi,
        md: ")"
      },
      {
        nx: /\[ascii-\]/gi,
        md: "`"
      },
      {
        nx: /\[-ascii\]/gi,
        md: "`"
      },
      {
        nx: /\[quote-\]/gi,
        md: "_"
      },
      {
        nx: /\[-quote\]/gi,
        md: "_"
      }
    ];

    for (let i = 0; i < Nx2MD.length; i++) {
      let re = new RegExp(Nx2MD[i].nx);
      text = text.replace(re, Nx2MD[i].md);
    }

    return text;
  }

  // https://regex101.com/r/YzSZVi/4
  static addUserMentions(text) {
    const regex = /(@)(\w*)/gm;
    const subst = `<span class="text-muted">@</span><mark><strong><a href="/users/$2">$2</a></strong></mark>`;
    const result = text.replace(regex, subst);

    return result;
  }

  // https://regex101.com/r/OY96XI/1
  static addYouTubeEmbed(text) {
    const regex = /(?:https?:)?(?:\/\/)?(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube(?:-nocookie)?\.com\S*?[^\w\s-])([\w-]{11})(?=[^\w-]|$)(?![?=&+%\w.-]*(?:['"][^<>]*>|<\/a>))[?=&+%\w.-]*/gim;
    const subst = `<div class="video-wrapper">
    <iframe id="youtube-player" src="//www.youtube.com/embed/$1?rel=0&showinfo=0&autohide=1" frameborder="0" allowfullscreen></iframe>
</div>`;
    const result = text.replace(regex, subst);

    return result;
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
