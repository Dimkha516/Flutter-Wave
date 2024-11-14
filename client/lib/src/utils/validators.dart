bool isValidPhoneNumber(String phone) {
  final regex = RegExp(r'^(77|76|75|70|78)\d{7}$');
  return regex.hasMatch(phone);
}
