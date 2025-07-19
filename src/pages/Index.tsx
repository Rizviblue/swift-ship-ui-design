import { Layout } from "@/components/courier/Layout";
import { AdminDashboard } from "@/components/courier/AdminDashboard";

const Index = () => {
  return (
    <Layout userRole="admin" userName="John Smith" userEmail="admin@courierpro.com">
      <AdminDashboard />
    </Layout>
  );
};

export default Index;
