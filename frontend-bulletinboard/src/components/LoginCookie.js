import Cookies from "universal-cookie";

const cookies = new Cookies();

export function setLoggedIn(loggedIn) {
  cookies.set("loggedIn", loggedIn, { path: "/" });
}

export function getLoggedIn(loggedIn) {
  return cookies.get("loggedIn");
}
