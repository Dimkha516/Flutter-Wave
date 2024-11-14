// ignore_for_file: prefer_const_constructors, prefer_const_literals_to_create_immutables

import 'package:flutter/material.dart';
import 'package:flutter_contacts/flutter_contacts.dart';

class MultipleTransferForm extends StatefulWidget {
  final Function(List<String>, double) onSendMultiple;

  const MultipleTransferForm({super.key, required this.onSendMultiple});

  @override
  // ignore: library_private_types_in_public_api
  _MultipleTransferFormState createState() => _MultipleTransferFormState();
}

class _MultipleTransferFormState extends State<MultipleTransferForm> {
  List<Contact> contacts = [];
  List<String> selectedContacts = [];
  bool isAmountFormVisible = false;
  final TextEditingController _amountController = TextEditingController();
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadContacts();
  }

  Future<void> _loadContacts() async {
    setState(() {
      _isLoading = true;
    });
    if (await FlutterContacts.requestPermission()) {
      final allContacts =
          await FlutterContacts.getContacts(withProperties: true, sorted: true);
      setState(() {
        contacts = allContacts;
        _isLoading = false;
      });
    }
  }

  void _toggleSelection(String phoneNumber) {
    setState(() {
      if (selectedContacts.contains(phoneNumber)) {
        selectedContacts.remove(phoneNumber);
      } else {
        selectedContacts.add(phoneNumber);
      }
    });
  }

  void _confirmAmount() {
    final amount = double.tryParse(_amountController.text);
    if (amount != null && amount > 0) {
      widget.onSendMultiple(selectedContacts, amount);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Veuillez entrer un montant valide')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: Text('Transferts Multiples'),
      content: isAmountFormVisible
          ? Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextFormField(
                  controller: _amountController,
                  decoration: InputDecoration(labelText: 'Montant par contact'),
                  keyboardType: TextInputType.number,
                ),
              ],
            )
          : Expanded(
              child: _isLoading
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          CircularProgressIndicator(), // Loader
                          SizedBox(
                              height: 10), // Espace entre le loader et le texte
                          Text(
                              "Chargement des contacts..."), // Texte sous le loader
                        ],
                      ),
                    )
                  : contacts.isEmpty
                      ? Center(
                          child: Text('Aucun contact trouvé'),
                        )
                      : ListView.builder(
                          itemCount: contacts.length,
                          itemBuilder: (context, index) {
                            final contact = contacts[index];
                            final phoneNumber = contact.phones.isNotEmpty
                                ? contact.phones.first.number
                                : 'Numéro non disponible';
                            return ListTile(
                              title: Text(contact.displayName),
                              subtitle: Text(phoneNumber),
                              trailing: Checkbox(
                                value: selectedContacts.contains(phoneNumber),
                                onChanged: (_) => _toggleSelection(phoneNumber),
                              ),
                            );
                          },
                        ),
            ),
      actions: [
        if (isAmountFormVisible)
          TextButton(
            onPressed: () {
              setState(() {
                isAmountFormVisible = false;
              });
            },
            child: Text('Annuler'),
          ),
        TextButton(
          onPressed: isAmountFormVisible
              ? _confirmAmount
              : () {
                  if (selectedContacts.isNotEmpty) {
                    setState(() {
                      isAmountFormVisible = true;
                    });
                  } else {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(
                          content: Text(
                              'Veuillez sélectionner au moins un contact')),
                    );
                  }
                },
          child: Text(isAmountFormVisible ? 'Envoyer' : 'Terminer'),
        ),
      ],
    );
  }
}
