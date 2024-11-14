// ignore_for_file: library_private_types_in_public_api, use_build_context_synchronously, prefer_const_constructors, prefer_const_literals_to_create_immutables
import 'package:client/src/services/api_service.dart';
import 'package:flutter/material.dart';
import 'package:flutter_contacts/flutter_contacts.dart';

class TransferForm extends StatefulWidget {
  final String token;

  const TransferForm({super.key, required this.token});

  @override
  _TransferFormState createState() => _TransferFormState();
}

class _TransferFormState extends State<TransferForm> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _phoneController = TextEditingController();
  final TextEditingController _amountController = TextEditingController();
  String? _errorMessage;
  List<Contact> _contacts = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _getContacts();
  }

  Future<void> _getContacts() async {
    setState(() {
      _isLoading = true; // Début du chargement.
    });
    if (await FlutterContacts.requestPermission()) {
      // Modification ici : ajout de withProperties: true
      final contacts = await FlutterContacts.getContacts(
          withProperties:
              true, // Cette ligne est cruciale pour obtenir les numéros
          sorted: true);
      setState(() {
        _contacts = contacts;
        _isLoading = false; // Fin du chargement.
      });
    }
  }

  Future<void> _sendMoney() async {
    final phone = _phoneController.text;
    final amount = double.tryParse(_amountController.text);

    if (amount == null) {
      setState(() {
        _errorMessage = "Veuillez entrer un montant valide.";
      });
      return;
    }

    final apiService = ApiService(token: widget.token);

    try {
      final response = await apiService.post(
        '/transactions/send',
        {
          'numero_destinataire': phone,
          'montant': amount,
          'type': 'envoi',
        },
      );
      if (response['message'] != null) {
        Navigator.of(context).pop();
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(response['message'])),
        );
      }
    } catch (e) {
      setState(() {
        _errorMessage = e.toString();
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: Text("Transfert d'argent"),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Expanded(
            child: SingleChildScrollView(
              child: Form(
                key: _formKey,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    TextFormField(
                      controller: _phoneController,
                      decoration:
                          InputDecoration(labelText: 'Numéro du destinataire'),
                      keyboardType: TextInputType.phone,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Veuillez entrer le numéro du destinataire';
                        }
                        return null;
                      },
                    ),
                    SizedBox(height: 16),
                    TextFormField(
                      controller: _amountController,
                      decoration: InputDecoration(labelText: 'Montant'),
                      keyboardType: TextInputType.number,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Veuillez entrer le montant';
                        }
                        return null;
                      },
                    ),
                    SizedBox(height: 8),
                    if (_errorMessage != null)
                      Padding(
                        padding: const EdgeInsets.only(top: 8.0),
                        child: Text(
                          _errorMessage!,
                          style: TextStyle(color: Colors.red),
                        ),
                      ),
                  ],
                ),
              ),
            ),
          ),
          Expanded(
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
                : _contacts.isEmpty
                    ? Center(
                        child: Text('Aucun contact trouvé'),
                      )
                    : ListView.builder(
                        itemCount: _contacts.length,
                        itemBuilder: (context, index) {
                          final contact = _contacts[index];
                          return ListTile(
                            title: Text(contact.displayName),
                            subtitle: contact.phones.isNotEmpty
                                ? Text(contact.phones.first.number)
                                : Text('Pas de numéro'),
                            onTap: () {
                              if (contact.phones.isNotEmpty) {
                                _phoneController.text =
                                    contact.phones.first.number;
                              }
                            },
                          );
                        },
                      ),
          ),
        ],
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.of(context).pop(),
          child: Text('Annuler'),
        ),
        ElevatedButton(
          onPressed: () {
            if (_formKey.currentState!.validate()) {
              _sendMoney();
            }
          },
          child: Text('Envoyer'),
        ),
      ],
    );
  }
}
