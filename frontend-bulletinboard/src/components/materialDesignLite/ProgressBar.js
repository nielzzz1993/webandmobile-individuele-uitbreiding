//React
import React, { Component } from "react";

export default class ProgressBar extends Component {
  render() {
    return (
      <div
        class="mdl-progress mdl-js-progress mdl-progress__indeterminate"
        style={{ width: "100%" }}
      />
    );
  }
}
