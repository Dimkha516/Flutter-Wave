// ignore_for_file: prefer_const_constructors, unused_element, avoid_print, use_build_context_synchronously

import 'package:client/src/components/multiple_transfer_form.dart';
import 'package:client/src/components/sidebar_menu.dart';
import 'package:client/src/components/transfer_form.dart';
import 'package:client/src/pages/scheduled_transactions_page.dart';
import 'package:client/src/services/api_service.dart';
import 'package:flutter/material.dart';
import '../widgets/balance_section.dart';
import '../widgets/qrcode_card.dart';
import '../widgets/action_buttons.dart';
import '../widgets/transaction_history.dart';
import '../services/auth_service.dart';

class HomePage extends StatelessWidget {
  final String token;
  final AuthService authService;

  const HomePage({super.key, required this.token, required this.authService});

  void _openMultipleTransfer(BuildContext context) {
    // Logique pour l'envoi multiple
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return MultipleTransferForm(
          onSendMultiple: (selectedContacts, amount) async {
            try {
              final result = await ApiService(token: token)
                  .sendMultipleTransactions(selectedContacts, amount);

              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                    content: Text(
                        'Transfert réussi: ${result['successful_transfers']}')),
              );
            } catch (error) {
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(content: Text('Erreur: ${error.toString()}')),
              );
            }
            Navigator.of(context).pop();
          },
        );
      },
    );
  }

  // Fonction pour ouvrir la page des transactions planifiées
  void _openScheduledTransactions(BuildContext context) {
    Navigator.of(context).push(MaterialPageRoute(
      builder: (context) => ScheduledTransactionsPage(
        apiService: ApiService(token: token),
      ),
    ));
  }

  // void _scheduleTransaction() {
  //   print('Transfert Planifiés');
  //   // Logique pour planifier une transaction
  // }

  void _logout(BuildContext context) async {
    // Logique pour la déconnexion
    final confirm = await showDialog<bool>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Déconnexion'),
          content: Text('Voulez-vous vraiment vous déconnecter ?'),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(false),
              child: Text('Annuler'),
            ),
            TextButton(
              onPressed: () => Navigator.of(context).pop(true),
              child: Text('Déconnexion'),
            ),
          ],
        );
      },
    );

    if (confirm == true) {
      try {
        await authService.logout();
        Navigator.of(context).pushNamedAndRemoveUntil('/', (route) => false);
      } catch (e) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erreur lors de la déconnexion : $e')),
        );
      }
    }
  }

  void _openTransferPopup(BuildContext context) {
    showDialog(
        context: context,
        builder: (BuildContext context) {
          return TransferForm(token: token);
        });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Mobile Money'),
        leading: Builder(
          builder: (BuildContext context) {
            return IconButton(
              icon: Icon(Icons.menu),
              onPressed: () => Scaffold.of(context).openDrawer(),
            );
          },
        ),
      ),
      drawer: SidebarMenu(
        onMultipleTransfer: () {
          _openMultipleTransfer(context);
        },
        onScheduleTransaction: () {
          _openScheduledTransactions(context);
        },
        onLogout: () {
          _logout(context);
        },
      ),
      body: Column(
        children: [
          BalanceSection(token: token),
          QrCodeCard(),
          ActionButtons(onTransferPressed: () => _openTransferPopup(context)),
          Expanded(
            child: TransactionHistory(apiService: ApiService(token: token)),
          ),
        ],
      ),
    );
  }
}
