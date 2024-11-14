import 'package:flutter_contacts/flutter_contacts.dart';

class ContactsService {
  Future<List<Contact>> getContacts() async {
    if (await FlutterContacts.requestPermission()) {
      final contacts = await FlutterContacts.getContacts(
        withAccounts: true,
        withPhoto: false,
      );
      return contacts;
    }
    return [];
  }
}
