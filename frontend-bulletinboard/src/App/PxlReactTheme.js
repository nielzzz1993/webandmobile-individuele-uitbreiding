import { createMuiTheme } from "@material-ui/core/styles";

let darkGreen = "#00B050";
let lightGreen = "#58A618";
let lightestGreen = "#5AAF15";
let lightGrey = "#ECECEC";
let white = "#FFF";

export default createMuiTheme({
  typography: {
    useNextVariants: true,
    h1: {
      color: darkGreen,
      fontSize: "22pt",
      paddingBottom: "5pt",
      textAlign: "left"
    },
    h5: {
      color: darkGreen,
      paddingBottom: "5pt"
    },
    body1: {
      fontSize: "14pt",
      textAlign: "left",
      textTransform: "none"
    },
    subtitle1: {
      color: white
    }
  },
  appBar: {
    position: "fixed",
    width: "100%"
  },
  palette: {
    primary: {
      main: lightGreen
    },
    secondary: {
      main: lightGrey
    }
  },
  overrides: {
    MuiInputBase: {
      root: {
        backgroundColor: lightGrey,
        borderWidth: "0",
        borderRadius: "5pt",
        margin: "8pt 0"
      }
    },
    MuiOutlinedInput: {
      root: {
        backgroundColor: lightGrey
      }
    },
    MuiCard: {
      root: {
        borderRadius: "5pt",
        boxShadow: "0 0 5pt 5pt #AAA",
        margin: "0 10pt 25pt"
      }
    },
    MuiCardContent: {
      root: {
        backgroundColor: lightGrey,
        textAlign: "left"
      }
    },
    MuiCardActions: {
      root: {
        backgroundColor: lightGrey,
        textAlign: "left"
      }
    },
    MuiList: {
      root: {
        backgroundColor: lightestGreen,
        overflow: "hidden",
        width: "100%"
      }
    },
    MuiPaper: {
      root: {
        backgroundColor: white,
        borderRadius: "10pt"
      }
    },
    MuiMenuItem: {
      root: {
        animation: "fadein 0.5s",
        color: white
      }
    },
    MuiToolbar: {
      root: {
        margin: "0 10pt",
        minHeight: "auto"
      }
    }
  }
});
