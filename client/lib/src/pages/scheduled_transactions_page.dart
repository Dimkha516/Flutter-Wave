// ignore_for_file: use_build_context_synchronously, prefer_const_constructors, prefer_const_literals_to_create_immutables
import 'package:flutter/material.dart';
import 'package:client/src/services/api_service.dart';
import 'package:intl/intl.dart';

class ScheduledTransactionsPage extends StatefulWidget {
  final ApiService apiService;

  const ScheduledTransactionsPage({super.key, required this.apiService});

  @override
  // ignore: library_private_types_in_public_api
  _ScheduledTransactionsPageState createState() =>
      _ScheduledTransactionsPageState();
}

class _ScheduledTransactionsPageState extends State<ScheduledTransactionsPage> {
  List<dynamic> scheduledTransactions = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchScheduledTransactions();
  }

  Future<void> _fetchScheduledTransactions() async {
    try {
      final transactions = await widget.apiService.getScheduledTransactions();
      setState(() {
        scheduledTransactions = transactions;
        isLoading = false;
      });
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Erreur: $e')),
      );
    }
  }

  void _addScheduledTransaction() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return _AddScheduledTransactionDialog(
          apiService: widget.apiService,
          onSuccess: (transaction) {
            setState(() {
              scheduledTransactions.add(transaction);
            });
            // Optionnel : Afficher un snack-bar de confirmation
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                  content: Text('Transaction planifiée ajoutée avec succès !')),
            );
          },
        );
      },
    );
  }

  void _cancelScheduledTransaction(int transactionId) async {
    final shouldCancel = await showDialog<bool>(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Text('Confirmer l\'annulation'),
          content: Text(
              'Voulez-vous vraiment annuler cette transaction planifiée ?'),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(false),
              child: Text('Non'),
            ),
            ElevatedButton(
              onPressed: () => Navigator.of(context).pop(true),
              child: Text('Oui'),
            ),
          ],
        );
      },
    );

    if (shouldCancel == true) {
      try {
        await widget.apiService.cancelScheduledTransaction(transactionId);
        setState(() {
          scheduledTransactions
              .removeWhere((transaction) => transaction['id'] == transactionId);
        });

        // Afficher une notification de succès
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Transaction annulée avec succès.')),
        );
      } catch (e) {
        // Afficher une notification d'erreur
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erreur : ${e.toString()}')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Transactions Planifiées'),
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator())
          : ListView.builder(
              itemCount: scheduledTransactions.length,
              itemBuilder: (context, index) {
                final transaction = scheduledTransactions[index];
                return ListTile(
                  title: Text('À ${transaction['numero_destinataire']}'),
                  subtitle: Text(
                      'Montant: ${transaction['montant']} - Date: ${transaction['date']}'),
                  trailing: ElevatedButton(
                    onPressed: () {
                      _cancelScheduledTransaction(transaction['id']);
                      // Annulation à implémenter si nécessaire
                    },
                    child: Text('Annuler'),
                  ),
                );
              },
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: _addScheduledTransaction,
        child: Icon(Icons.add),
      ),
    );
  }
}

class _AddScheduledTransactionDialog extends StatefulWidget {
  final ApiService apiService;
  final Function(Map<String, dynamic>) onSuccess;

  const _AddScheduledTransactionDialog(
      {required this.apiService, required this.onSuccess});

  @override
  _AddScheduledTransactionDialogState createState() =>
      _AddScheduledTransactionDialogState();
}

class _AddScheduledTransactionDialogState
    extends State<_AddScheduledTransactionDialog> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _phoneController = TextEditingController();
  final TextEditingController _amountController = TextEditingController();
  // final TextEditingController _dateController = TextEditingController();
  DateTime? _selectedDate;
  String? _selectedFrequency;
  String? _errorMessage;

  get scheduledTransactions => null;

  Future<void> _submit() async {
    if (_formKey.currentState?.validate() == true) {
      try {
        final transaction = await widget.apiService.addScheduledTransaction(
          _phoneController.text,
          double.parse(_amountController.text),
          _selectedDate!.toIso8601String(),
          frequency: _selectedFrequency ?? 'monthly',
        );
        widget.onSuccess(transaction['data']);
        Navigator.of(context).pop();
      } catch (e) {
        setState(() {
          _errorMessage = e.toString();
        });
      }
    }
  }

//

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: Text('Ajouter une transaction planifiée'),
      content: SingleChildScrollView(
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              TextFormField(
                controller: _phoneController,
                decoration:
                    InputDecoration(labelText: 'Numéro du destinataire'),
                validator: (value) =>
                    value == null || value.isEmpty ? 'Champ obligatoire' : null,
              ),
              TextFormField(
                controller: _amountController,
                keyboardType: TextInputType.number,
                decoration: InputDecoration(labelText: 'Montant'),
                validator: (value) =>
                    value == null || value.isEmpty ? 'Champ obligatoire' : null,
              ),
              ListTile(
                title: Text(_selectedDate == null
                    ? 'Sélectionner une date'
                    : DateFormat('yyyy-MM-dd').format(_selectedDate!)),
                trailing: Icon(Icons.calendar_today),
                onTap: () async {
                  final pickedDate = await showDatePicker(
                    context: context,
                    initialDate: DateTime.now(),
                    firstDate: DateTime.now(),
                    lastDate: DateTime(2100),
                  );
                  if (pickedDate != null) {
                    setState(() {
                      _selectedDate = pickedDate;
                    });
                  }
                },
              ),
              DropdownButtonFormField<String>(
                value: _selectedFrequency,
                decoration: InputDecoration(labelText: 'Fréquence'),
                items: [
                  DropdownMenuItem(
                    value: 'monthly',
                    child: Text('Mensuel'),
                  ),
                  DropdownMenuItem(
                    value: 'weekly',
                    child: Text('Hebdomadaire'),
                  ),
                  DropdownMenuItem(
                    value: 'daily',
                    child: Text('Quotidien'),
                  ),
                ],
                onChanged: (value) {
                  setState(() {
                    _selectedFrequency = value;
                  });
                },
              ),
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
      actions: [
        TextButton(
          onPressed: () => Navigator.of(context).pop(),
          child: Text('Annuler'),
        ),
        ElevatedButton(
          onPressed: _submit,
          child: Text('Ajouter'),
        ),
      ],
    );
  }
}
