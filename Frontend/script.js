// ---------- Validation rules (regular expressions) ----------
const RULES = {
  name:  /^[A-Za-z][A-Za-z .]{2,59}$/,                    // letters, spaces, dots
  roll:  /^[0-9]{2}[A-Za-z]{2,4}[0-9]{3}$/,               // e.g. 24CS082
  email: /^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/                // basic e-mail shape
};
const MAX_BYTES = 2 * 1024 * 1024;                          // 2 MB upload limit
 
// ---------- Small helpers ----------
const $ = (id) => document.getElementById(id);
 
function setError(field, message) {
  $("err-" + field).textContent = message;
  $(field).classList.toggle("invalid", message !== "");
  return message === "";                                    // true when valid
}
 
// Returns a score from 0 to 4 for a password
function passwordScore(p) {
  let s = 0;
  if (p.length >= 8) s++;
  if (/[a-z]/.test(p) && /[A-Z]/.test(p)) s++;
  if (/[0-9]/.test(p)) s++;
  if (/[^A-Za-z0-9]/.test(p)) s++;
  return s;
}
 
// Checks one optional file input for type and size
function checkFile(id, allowed) {
  const input = $(id);
  if (input.files.length === 0) return setError(id, "");   // upload is optional
  const file = input.files[0];
  const ext = file.name.split(".").pop().toLowerCase();
  if (!allowed.includes(ext))
    return setError(id, "Allowed types: " + allowed.join(", ").toUpperCase());
  if (file.size > MAX_BYTES)
    return setError(id, "File is larger than 2 MB.");
  return setError(id, "");
}
 
// ---------- Individual field validators ----------
function vName() {
  const v = $("name").value.trim();
  if (v === "") return setError("name", "Name is required.");
  return setError("name", RULES.name.test(v) ? "" : "Use 3-60 letters, spaces or dots only.");
}
function vRoll() {
  const v = $("roll").value.trim();
  if (v === "") return setError("roll", "Roll number is required.");
  return setError("roll", RULES.roll.test(v) ? "" : "Format: 2 digits + branch + 3 digits (24CS082).");
}
function vEmail() {
  const v = $("email").value.trim();
  if (v === "") return setError("email", "Email is required.");
  return setError("email", RULES.email.test(v) ? "" : "Enter a valid email address.");
}
function vPassword() {
  const v = $("password").value;
  if (v === "") return setError("password", "Password is required.");
  return setError("password", passwordScore(v) === 4 ? "" :
    "Need 8+ characters with upper, lower, digit and symbol.");
}
function vCourse() {
  return setError("course", $("course").value === "" ? "Please choose a course." : "");
}
 
// ---------- Live password strength meter ----------
$("password").addEventListener("input", () => {
  const score = passwordScore($("password").value);
  const colours = ["#c62828", "#ef6c00", "#f9a825", "#7cb342", "#2e7d32"];
  const labels  = ["Very weak", "Weak", "Fair", "Good", "Strong"];
  $("meterBar").style.width = (score * 25) + "%";
  $("meterBar").style.background = colours[score];
  $("meterText").textContent = labels[score];
});
 
// Validate each field when the user leaves it
$("name").addEventListener("blur", vName);
$("roll").addEventListener("blur", vRoll);
$("email").addEventListener("blur", vEmail);
$("course").addEventListener("change", vCourse);
$("photo").addEventListener("change", () => checkFile("photo", ["jpg", "jpeg", "png"]));
$("document").addEventListener("change", () => checkFile("document", ["pdf", "jpg", "jpeg", "png"]));
 
// ---------- Final check on submit ----------
$("regForm").addEventListener("submit", (event) => {
  // run every validator (no short-circuit, so all messages show together)
  const results = [
    vName(), vRoll(), vEmail(), vPassword(), vCourse(),
    checkFile("photo", ["jpg", "jpeg", "png"]),
    checkFile("document", ["pdf", "jpg", "jpeg", "png"])
  ];
  if (results.includes(false)) {
    event.preventDefault();                                  // stop submission
    document.querySelector(".invalid").focus();              // jump to first error
  }
});
 
// Reset button also clears messages and the meter
$("regForm").addEventListener("reset", () => {
  document.querySelectorAll(".error").forEach(e => e.textContent = "");
  document.querySelectorAll(".invalid").forEach(e => e.classList.remove("invalid"));
  $("meterBar").style.width = "0";
  $("meterText").textContent = "Minimum 8 characters with upper, lower, digit and symbol";
});
