// ignore_for_file: use_super_parameters, prefer_const_constructors
import 'package:flutter/material.dart';

class SidebarMenu extends StatelessWidget {
  final VoidCallback onMultipleTransfer;
  final VoidCallback onScheduleTransaction;
  final VoidCallback onLogout;

  const SidebarMenu({
    Key? key,
    required this.onMultipleTransfer,
    required this.onScheduleTransaction,
    required this.onLogout,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Drawer(
      child: ListView(
        padding: EdgeInsets.zero,
        children: [
          DrawerHeader(
            decoration: BoxDecoration(color: Colors.blue),
            child: Text(
              'Menu',
              style: TextStyle(color: Colors.white, fontSize: 24),
            ),
          ),
          ListTile(
            leading: Icon(Icons.send),
            title: Text('Envoi multiple'),
            onTap: onMultipleTransfer,
          ),
          ListTile(
            leading: Icon(Icons.schedule),
            title: Text('Planifier transaction'),
            onTap: onScheduleTransaction,
          ),
          ListTile(
            leading: Icon(Icons.logout),
            title: Text('Déconnexion'),
            onTap: onLogout,
          ),
        ],
      ),
    );
  }
}
