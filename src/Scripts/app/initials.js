 window.getInitials = function (text) {
    const initials = text
      .split(/\s+/)
      .map((word) => word[0])
      .join("")
      .slice(0, 2)
      .toUpperCase();
    return initials;
  }